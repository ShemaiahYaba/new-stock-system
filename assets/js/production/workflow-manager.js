/**
 * Workflow Manager Module
 * File: assets/js/production/workflow-manager.js
 *
 * Orchestrates the entire production workflow
 * Manages state, property rows, and calculations
 */

class WorkflowManager {
  constructor() {
    this.state = {
      customer: null,
      warehouse: null,
      coil: null,
      stockEntry: null,
      properties: new Map(), // rowId => calculationResult
      productionSummary: {
        totalMeters: 0,
        totalAmount: 0,
      },
      invoiceData: {
        tax: { type: "fixed", value: 0, amount: 0 },
        discount: { type: "fixed", value: 0, amount: 0 },
        shipping: 0,
        grandTotal: 0,
        notes: "",
      },
    };

    this.rowCounter = 0;
    this.availableProperties = [];
    this.availableAddons = [];
    this.currentCategory = null;

    // ✅ CRITICAL: Initialize add-ons state
    this.initializeAddonState();
  }

  /**
   * FIXED handleCoilSelection Method
   * Replace the existing handleCoilSelection method in workflow-manager.js
   * Location: Around line 45-90 in workflow-manager.js
   */

  /**
   * Handle coil selection - FIXED VERSION
   * @param {Object} coilData - The selected coil data
   */
  async handleCoilSelection(coilData) {
    try {
      console.log("🔍 handleCoilSelection called with:", coilData);

      // Update the state with the selected coil
      this.state.coil = coilData;
      this.currentCategory = coilData.category.toLowerCase();

      // Clear any existing stock entry and properties
      this.state.stockEntry = null;
      this.state.properties.clear();

      const stockEntrySelect = document.getElementById("stock_entry_id");
      if (!stockEntrySelect) {
        console.error("❌ Stock entry select element not found");
        return;
      }

      // Reset stock entry dropdown
      stockEntrySelect.innerHTML =
        '<option value="">-- Select Stock Entry --</option>';
      stockEntrySelect.disabled = true;

      // Hide properties and add-ons
      this.hidePropertiesAndAddons();

      // Check if this is KZINC (no stock entries needed)
      const isKzinc = this.currentCategory === "kzinc";

      if (isKzinc) {
        console.log("✅ KZINC detected - bypassing stock entry requirement");

        // Hide stock entry dropdown for KZINC
        const stockEntryCol = stockEntrySelect.closest(".col-md-6");
        if (stockEntryCol) {
          stockEntryCol.style.display = "none";
        }

        // Set dummy stock entry for KZINC
        this.state.stockEntry = {
          id: "kzinc_bypass",
          status: "available",
          meters_remaining: 0,
        };

        // Update available meters display
        const availableEl = document.getElementById("coil_available");
        if (availableEl) availableEl.textContent = "N/A";

        // Load properties and add-ons for KZINC
        await this.loadPropertiesForCategory(this.currentCategory);
        await this.loadAddonsForCategory(this.currentCategory);

        // Show properties immediately for KZINC
        this.showPropertiesForCategory();

        return; // Exit early for KZINC
      }

      // ============================================================
      // STOCK-BASED WORKFLOW (ALUSTEEL & ALUMINUM)
      // ============================================================

      console.log("📦 Stock-based category detected:", this.currentCategory);

      // Show stock entry dropdown for stock-based categories
      const stockEntryCol = stockEntrySelect.closest(".col-md-6");
      if (stockEntryCol) {
        stockEntryCol.style.display = "block";
      }

      // Show loading state
      stockEntrySelect.innerHTML =
        '<option value="">Loading stock entries...</option>';
      stockEntrySelect.disabled = true;

      // Fetch stock entries
      console.log("🔄 Fetching stock entries for coil:", coilData.id);

      const response = await fetch(
        `/new-stock-system/controllers/sales/get_stock_entries.php?coil_id=${coilData.id}`
      );

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      console.log("📥 Stock entries response:", data);

      if (data.success && data.entries && data.entries.length > 0) {
        // Reset dropdown with empty option
        stockEntrySelect.innerHTML =
          '<option value="">-- Select Stock Entry --</option>';

        let totalAvailable = 0;
        let addedCount = 0;

        // Filter entries with available meters
        const availableEntries = data.entries.filter((entry) => {
          const metersRemaining = parseFloat(entry.meters_remaining);
          return !isNaN(metersRemaining) && metersRemaining > 0;
        });

        console.log(
          `✅ Found ${availableEntries.length} entries with available meters`
        );

        if (availableEntries.length > 0) {
          // Add entries to dropdown
          availableEntries.forEach((entry) => {
            const metersRemaining = parseFloat(entry.meters_remaining);
            const opt = document.createElement("option");
            opt.value = entry.id;
            opt.textContent = `#${entry.id} - ${metersRemaining.toFixed(2)}m (${
              entry.status
            })`;
            opt.dataset.status = entry.status;
            opt.dataset.meters = metersRemaining;
            stockEntrySelect.appendChild(opt);

            totalAvailable += metersRemaining;
            addedCount++;
          });

          console.log(`✅ Added ${addedCount} stock entries to dropdown`);
          console.log(
            `📊 Total available meters: ${totalAvailable.toFixed(2)}m`
          );

          // Update available meters display
          const availableEl = document.getElementById("coil_available");
          if (availableEl) {
            availableEl.textContent = totalAvailable.toFixed(2);
          }

          // ✅ CRITICAL FIX: Enable the dropdown!
          stockEntrySelect.disabled = false;
          console.log("✅ Stock entry dropdown ENABLED");
        } else {
          // No entries with available meters
          stockEntrySelect.innerHTML =
            '<option value="">No stock entries with available meters</option>';
          stockEntrySelect.disabled = true;

          const availableEl = document.getElementById("coil_available");
          if (availableEl) availableEl.textContent = "0.00";

          console.warn("⚠️ No stock entries with available meters found");
        }
      } else {
        // No stock entries found
        stockEntrySelect.innerHTML =
          '<option value="">No available stock entries</option>';
        stockEntrySelect.disabled = true;

        const availableEl = document.getElementById("coil_available");
        if (availableEl) availableEl.textContent = "0.00";

        console.warn("⚠️ No stock entries returned from server");
      }

      // Load properties and add-ons for this category
      await this.loadPropertiesForCategory(this.currentCategory);
      await this.loadAddonsForCategory(this.currentCategory);

      console.log("✅ Properties and add-ons loaded for", this.currentCategory);
    } catch (error) {
      console.error("❌ Error in handleCoilSelection:", error);

      const stockEntrySelect = document.getElementById("stock_entry_id");
      if (stockEntrySelect) {
        stockEntrySelect.innerHTML =
          '<option value="">Error loading entries</option>';
        stockEntrySelect.disabled = true;
      }

      alert("Error loading stock entries: " + error.message);
    }
  }

  /**
   * Helper method to hide properties and add-ons sections
   */
  hidePropertiesAndAddons() {
    const propertiesContainer = document.getElementById("properties_container");
    const addonsSection = document.getElementById("addons_section");
    const summaryBox = document.getElementById("production_summary");

    if (propertiesContainer) propertiesContainer.classList.add("d-none");
    if (addonsSection) addonsSection.classList.add("d-none");
    if (summaryBox) summaryBox.classList.add("d-none");

    // Clear property rows
    const propertyRows = document.getElementById("property_rows");
    if (propertyRows) propertyRows.innerHTML = "";

    // Clear properties state
    this.state.properties.clear();
  }

  /**
   * Helper method to show properties based on category
   */
  showPropertiesForCategory() {
    console.log(
      "📋 showPropertiesForCategory called for:",
      this.currentCategory
    );

    const propertiesContainer = document.getElementById("properties_container");
    const addonsSection = document.getElementById("addons_section");

    if (!propertiesContainer) {
      console.error("❌ Properties container not found");
      return;
    }

    // Show properties container
    propertiesContainer.classList.remove("d-none");

    // Show add-ons section if add-ons are available
    if (this.availableAddons && this.availableAddons.length > 0) {
      this.showAddonSelectionUI();
    }

    // Add first property row if none exist
    if (
      this.state.properties.size === 0 &&
      this.availableProperties.length > 0
    ) {
      console.log("➕ Adding initial property row");
      this.addPropertyRowWithSelection();
    }

    console.log("✅ Properties section shown");
  }

  /**
   * Reset all properties and UI elements
   */
  resetProperties() {
    this.state.properties.clear();
    this.rowCounter = 0;
    document.getElementById("property_rows").innerHTML = "";
    this.calculateProductionTotals();
  }

  /**
   * Load properties for a specific category
   */
  async loadPropertiesForCategory(category) {
    try {
      const response = await fetch(
        `/new-stock-system/controllers/production_properties/get_by_category.php?category=${category}`
      );

      const data = await response.json();

      if (data.success) {
        this.availableProperties = data.properties;
        this.currentCategory = category;
        return data.properties;
      } else {
        console.error("Failed to load properties:", data.message);
        return [];
      }
    } catch (error) {
      console.error("Error loading properties:", error);
      return [];
    }
  }

  /**
   * Show property selection UI based on category
   */
  showPropertySelectionUI(category) {
    // Hide all property panes first
    const alusteelPane = document.getElementById("property_selection_alusteel");
    const kzincPane = document.getElementById("property_selection_kzinc");

    if (alusteelPane) alusteelPane.classList.add("d-none");
    if (kzincPane) kzincPane.classList.add("d-none");

    // Show appropriate pane based on category
    if (category === "kzinc") {
      if (kzincPane) kzincPane.classList.remove("d-none");
    } else if (category === "alusteel" || category === "aluminum") {
      if (alusteelPane) alusteelPane.classList.remove("d-none");
    }

    // If no properties exist yet, add first row
    if (this.state.properties.size === 0) {
      this.addPropertyRowWithSelection();
    }
  }

  /**
   * Add a property row with property selection dropdown
   */
  addPropertyRowWithSelection() {
    if (this.availableProperties.length === 0) {
      alert(
        "No properties available for this category. Please create properties first."
      );
      return;
    }

    const rowId = this.rowCounter++;
    const container = this.getPropertiesContainer();

    if (!container) {
      console.error("Properties container not found");
      return;
    }

    // Create property selection HTML
    const selectionHtml = `
            <div class="mb-3" id="property_selector_${rowId}">
                <label class="form-label">Select Property <span class="text-danger">*</span></label>
                <select class="form-select property-selector" data-row-id="${rowId}">
                    ${PropertyRenderer.renderPropertyDropdown(
                      this.availableProperties
                    )}
                </select>
            </div>
            <div id="property_form_${rowId}"></div>
        `;

    const wrapper = document.createElement("div");
    wrapper.id = `property_wrapper_${rowId}`;
    wrapper.className = "mb-4";
    wrapper.innerHTML = selectionHtml;

    container.appendChild(wrapper);

    // Attach selector listener
    const selector = wrapper.querySelector(".property-selector");
    selector.addEventListener("change", (e) => {
      this.onPropertySelected(rowId, e.target);
    });
  }

  /**
   * Handle property selection from dropdown
   */
  onPropertySelected(rowId, selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];

    if (!selectedOption.value) {
      // Clear the form if no selection
      const formContainer = document.getElementById(`property_form_${rowId}`);
      if (formContainer) formContainer.innerHTML = "";
      return;
    }

    try {
      const property = JSON.parse(selectedOption.dataset.property);
      this.renderPropertyForm(rowId, property);
    } catch (error) {
      console.error("Error parsing property data:", error);
    }
  }

  /**
   * Render the property form after selection
   */
  renderPropertyForm(rowId, property) {
    const formContainer = document.getElementById(`property_form_${rowId}`);
    if (!formContainer) return;

    const formHtml = PropertyRenderer.renderRow(property, rowId);
    formContainer.innerHTML = formHtml;

    // Attach listeners
    PropertyRenderer.attachListeners(rowId, property);

    // Store property reference
    this.state.properties.set(rowId, {
      property: property,
      calculation: null,
    });
  }

  /**
   * Add a property row (direct, without selection)
   */
  addPropertyRow(property) {
    const rowId = this.rowCounter++;
    const container = this.getPropertiesContainer();

    if (!container) {
      console.error("Properties container not found");
      return;
    }

    const html = PropertyRenderer.renderRow(property, rowId);
    container.insertAdjacentHTML("beforeend", html);

    PropertyRenderer.attachListeners(rowId, property);

    this.state.properties.set(rowId, {
      property: property,
      calculation: null,
    });
  }

  /**
   * Calculate a specific property row
   */
  calculatePropertyRow(rowId, property) {
    const row = document.getElementById(`property_row_${rowId}`);
    if (!row) return;

    const inputs = this.collectRowInputs(row, property.property_type);

    // Don't calculate until the required inputs are present
    if (property.property_type === "meter_based") {
      if (!inputs.sheetQty || inputs.sheetQty <= 0) return;
      if (!inputs.sheetMeter || inputs.sheetMeter <= 0) return;
    } else {
      if (!inputs.quantity || inputs.quantity <= 0) return;
    }

    // Unit price can be blank (calculator will fallback to default_price); only block negatives
    if (typeof inputs.unitPrice === "number" && inputs.unitPrice < 0) return;

    const result = PropertyCalculator.calculate(property, inputs);

    // Update UI with results
    this.updateRowDisplay(rowId, result, property.property_type);

    // Store calculation in state
    const stateEntry = this.state.properties.get(rowId);
    if (stateEntry) {
      stateEntry.calculation = result;
    }

    // Recalculate totals
    this.calculateProductionTotals();
  }

  /**
   * Collect inputs from a row
   */
  collectRowInputs(row, propertyType) {
    const inputs = {};

    switch (propertyType) {
      case "meter_based":
        inputs.sheetQty =
          parseFloat(row.querySelector(".sheet-qty")?.value) || 0;
        inputs.sheetMeter =
          parseFloat(row.querySelector(".sheet-meter")?.value) || 0;
        {
          const raw = row.querySelector(".unit-price")?.value;
          inputs.unitPrice =
            raw === "" || raw === undefined || raw === null
              ? null
              : parseFloat(raw);
        }
        break;

      case "unit_based":
      case "bundle_based":
        inputs.quantity =
          parseFloat(row.querySelector(".kzinc-quantity")?.value) || 0;
        {
          const raw = row.querySelector(".kzinc-unit-price")?.value;
          inputs.unitPrice =
            raw === "" || raw === undefined || raw === null
              ? null
              : parseFloat(raw);
        }
        break;
    }

    return inputs;
  }

  /**
   * Update row display with calculation results
   */
  updateRowDisplay(rowId, result, propertyType) {
    switch (propertyType) {
      case "meter_based":
        const metersEl = document.getElementById(`meters_${rowId}`);
        if (metersEl) {
          metersEl.value = result.meters.toFixed(2) + "m";
        }
        break;

      case "bundle_based":
        const piecesEl = document.getElementById(`pieces_value_${rowId}`);
        if (piecesEl) {
          piecesEl.value = result.pieces + " pieces";
        }
        break;
    }

    // Update subtotal (common for all types)
    const subtotalId =
      propertyType === "meter_based"
        ? `subtotal_${rowId}`
        : `kzinc_subtotal_${rowId}`;
    const subtotalEl = document.getElementById(subtotalId);
    if (subtotalEl) {
      subtotalEl.value = PropertyCalculator.formatCurrency(result.subtotal);
    }
  }

  // ============================================================
  // FIX calculateProductionTotals method (around line 1050)
  // ============================================================
  /**
   * Calculate production totals
   */
  calculateProductionTotals() {
    let totalMeters = 0;
    let totalAmount = 0;

    this.state.properties.forEach((entry) => {
      if (entry.calculation) {
        totalMeters += parseFloat(entry.calculation.meters) || 0;
        totalAmount += parseFloat(entry.calculation.subtotal) || 0;
      }
    });

    this.state.productionSummary = { totalMeters, totalAmount };

    // Update UI
    const metersDisplay = document.getElementById("total_meters_display");
    const amountDisplay = document.getElementById("production_total_display");

    if (metersDisplay) {
      metersDisplay.textContent =
        totalMeters > 0 ? totalMeters.toFixed(2) + "m" : "N/A";
    }

    if (amountDisplay) {
      amountDisplay.textContent =
        PropertyCalculator.formatCurrency(totalAmount);
    }

    // Show/hide summary box
    const summaryBox = document.getElementById("production_summary");
    if (summaryBox) {
      if (this.state.properties.size > 0 && totalAmount > 0) {
        summaryBox.classList.remove("d-none");
      } else {
        summaryBox.classList.add("d-none");
      }
    }

    // ✅ FIX: Only recalculate add-ons if they exist
    if (
      this.state.addons &&
      this.state.addons.selected &&
      this.state.addons.selected.size > 0
    ) {
      this.calculateAllAddons();
    } else {
      // Just update grand total without add-ons
      this.updateGrandTotal();
    }

    this.validateProductionTab();
  }
  /**
   * Remove a property row
   */
  removePropertyRow(rowId) {
    if (!confirm("Remove this property row?")) return;

    // Remove from DOM
    const wrapper = document.getElementById(`property_wrapper_${rowId}`);
    const row = document.getElementById(`property_row_${rowId}`);

    if (wrapper) wrapper.remove();
    else if (row) row.remove();

    // Remove from state
    this.state.properties.delete(rowId);

    // Recalculate
    this.calculateProductionTotals();
  }

  /**
   * Validate production tab
   */
  validateProductionTab() {
    const hasCustomer = !!this.state.customer;
    const hasWarehouse = !!this.state.warehouse;
    const hasCoil = !!this.state.coil;
    const hasStockEntry = !!this.state.stockEntry;
    const hasProperties = this.state.properties.size > 0;
    const hasValidAmount = this.state.productionSummary?.totalAmount > 0;

    const isValid =
      hasCustomer &&
      hasWarehouse &&
      hasCoil &&
      hasStockEntry &&
      hasProperties &&
      hasValidAmount;

    const btn = document.getElementById("proceed_to_invoice_btn");
    if (btn) {
      btn.disabled = !isValid;
    }

    return isValid;
  }

  /**
   * Get properties container based on current category
   */
  getPropertiesContainer() {
    return document.getElementById("property_rows");
  }

  resetCoilAndProperties() {
    this.state.coil = null;
    this.currentCategory = null;
    this.state.stockEntry = null;
    this.hidePropertiesAndAddons();

    const stockEntrySelect = document.getElementById("stock_entry_id");
    if (stockEntrySelect) {
      stockEntrySelect.innerHTML =
        '<option value="">-- Select Stock Entry --</option>';
      stockEntrySelect.disabled = true;
      const stockEntryCol = stockEntrySelect.closest(".col-md-6");
      if (stockEntryCol) stockEntryCol.style.display = "block";
    }

    const availableEl = document.getElementById("coil_available");
    if (availableEl) availableEl.textContent = "0.00";

    this.calculateProductionTotals();
  }

  proceedToInvoice() {
    if (!this.validateProductionTab()) {
      alert("Please complete the Production tab before proceeding to invoice.");
      return;
    }

    const customerNameEl = document.getElementById("invoice_customer");
    if (customerNameEl) {
      customerNameEl.textContent = this.state.customer?.name || "-";
    }

    const companyEl = document.getElementById("customer_company");
    if (companyEl) {
      const value = this.state.customer?.company || "";
      companyEl.textContent = value;
      companyEl.style.display = value ? "block" : "none";
    }

    const phoneEl = document.getElementById("customer_phone");
    if (phoneEl) {
      const value = this.state.customer?.phone || "";
      phoneEl.textContent = value;
      phoneEl.style.display = value ? "block" : "none";
    }

    const addressEl = document.getElementById("customer_address");
    if (addressEl) {
      const value = this.state.customer?.address || "";
      addressEl.textContent = value;
      addressEl.style.display = value ? "block" : "none";
    }

    const warehouseEl = document.getElementById("invoice_warehouse");
    if (warehouseEl) {
      warehouseEl.textContent = this.state.warehouse?.name || "-";
    }

    const invoiceNumberEl = document.getElementById("invoice_number");
    if (
      invoiceNumberEl &&
      (!invoiceNumberEl.textContent || invoiceNumberEl.textContent === "-")
    ) {
      invoiceNumberEl.textContent = "DRAFT";
    }

    const items = this.buildInvoiceItems();
    this.renderInvoiceItems(items);

    this.updateInvoiceAdjustments({
      tax_type: document.getElementById("tax_type")?.value || "fixed",
      tax_value: parseFloat(document.getElementById("tax_value")?.value) || 0,
      discount_type: document.getElementById("discount_type")?.value || "fixed",
      discount_value:
        parseFloat(document.getElementById("discount_value")?.value) || 0,
      shipping: parseFloat(document.getElementById("shipping")?.value) || 0,
    });

    const invoiceTabEl = document.getElementById("invoice-tab");
    if (invoiceTabEl && window.bootstrap?.Tab) {
      new bootstrap.Tab(invoiceTabEl).show();
    }
  }

  updateInvoiceAdjustments(adjustments) {
    const items = this.buildInvoiceItems();
    const subtotal = items.reduce((sum, item) => {
      const qty = parseFloat(item.quantity) || 0;
      const unit = parseFloat(item.unit_price) || 0;
      return sum + qty * unit;
    }, 0);

    const taxType = adjustments?.tax_type || "fixed";
    const taxValue = parseFloat(adjustments?.tax_value) || 0;
    const discountType = adjustments?.discount_type || "fixed";
    const discountValue = parseFloat(adjustments?.discount_value) || 0;
    const shipping = parseFloat(adjustments?.shipping) || 0;

    const taxAmount =
      taxType === "percentage" ? (subtotal * taxValue) / 100 : taxValue;
    const discountAmount =
      discountType === "percentage"
        ? (subtotal * discountValue) / 100
        : discountValue;

    const total = subtotal + taxAmount + shipping - discountAmount;

    this.state.invoiceData.tax = {
      type: taxType,
      value: taxValue,
      amount: taxAmount,
    };
    this.state.invoiceData.discount = {
      type: discountType,
      value: discountValue,
      amount: discountAmount,
    };
    this.state.invoiceData.shipping = shipping;
    this.state.invoiceData.grandTotal = total;

    const notesEl = document.getElementById("invoice_notes");
    this.state.invoiceData.notes = notesEl ? notesEl.value : "";

    const setText = (id, value) => {
      const el = document.getElementById(id);
      if (el) el.textContent = PropertyCalculator.formatCurrency(value);
    };

    setText("subtotal_amount", subtotal);
    setText("tax_amount", taxAmount);
    setText("shipping_amount", shipping);
    setText("total_amount", total);

    const discountEl = document.getElementById("discount_amount");
    if (discountEl) {
      discountEl.textContent =
        "-" + PropertyCalculator.formatCurrency(discountAmount);
    }
  }

  proceedToConfirm() {
    const items = this.buildInvoiceItems();
    if (items.length === 0) {
      alert("No invoice items found. Please add production properties first.");
      return;
    }

    this.updateInvoiceAdjustments({
      tax_type: document.getElementById("tax_type")?.value || "fixed",
      tax_value: parseFloat(document.getElementById("tax_value")?.value) || 0,
      discount_type: document.getElementById("discount_type")?.value || "fixed",
      discount_value:
        parseFloat(document.getElementById("discount_value")?.value) || 0,
      shipping: parseFloat(document.getElementById("shipping")?.value) || 0,
    });

    const productionPayload = this.prepareProductionPayload();
    const invoicePayload = this.prepareInvoicePayload();

    const productionInput = document.getElementById("production_data_input");
    if (productionInput)
      productionInput.value = JSON.stringify(productionPayload);
    const invoiceInput = document.getElementById("invoice_data_input");
    if (invoiceInput) invoiceInput.value = JSON.stringify(invoicePayload);

    const productionPreview = document.getElementById(
      "production_paper_preview"
    );
    if (productionPreview) {
      productionPreview.innerHTML = this.renderProductionPreviewHtml(
        productionPayload.production_paper
      );
    }

    const invoicePreview = document.getElementById("invoice_shape_preview");
    if (invoicePreview) {
      invoicePreview.innerHTML = this.renderInvoicePreviewHtml(invoicePayload);
    }

    const confirmTabEl = document.getElementById("confirm-tab");
    if (confirmTabEl && window.bootstrap?.Tab) {
      new bootstrap.Tab(confirmTabEl).show();
    }
  }

  async submitOrder() {
    const form = document.getElementById("confirm_order_form");
    if (!form) {
      throw new Error("Confirm order form not found");
    }

    const productionPayload = this.prepareProductionPayload();
    const invoicePayload = this.prepareInvoicePayload();

    const productionInput = document.getElementById("production_data_input");
    if (productionInput)
      productionInput.value = JSON.stringify(productionPayload);
    const invoiceInput = document.getElementById("invoice_data_input");
    if (invoiceInput) invoiceInput.value = JSON.stringify(invoicePayload);

    const formData = new FormData(form);
    formData.set("production_data", productionInput?.value || "{}");
    formData.set("invoice_data", invoiceInput?.value || "{}");

    const response = await fetch(
      "/new-stock-system/controllers/sales/create_workflow/index.php",
      {
        method: "POST",
        body: formData,
      }
    );

    const data = await response.json();
    return data;
  }

  buildInvoiceItems() {
    const items = [];

    this.state.properties.forEach((entry) => {
      if (!entry?.calculation) return;

      const calc = entry.calculation;
      const propertyType = entry.property?.property_type;

      const quantity =
        propertyType === "meter_based"
          ? parseFloat(calc.meters) || 0
          : parseFloat(calc.quantity) || 0;

      items.push({
        description: calc.propertyName || entry.property?.name || "Item",
        quantity: quantity,
        unit_price: parseFloat(calc.unitPrice) || 0,
        subtotal: parseFloat(calc.subtotal) || 0,
        is_addon: false,
      });
    });

    const addonItems = this.getAddonsForInvoice
      ? this.getAddonsForInvoice()
      : [];
    addonItems.forEach((a) => items.push(a));

    return items;
  }

  renderInvoiceItems(items) {
    const tbody = document.getElementById("invoice_items_body");
    if (!tbody) return;

    if (!items || items.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" class="text-center text-muted py-4">No items added yet</td>
        </tr>
      `;
      return;
    }

    tbody.innerHTML = items
      .map((item, idx) => {
        const qty = parseFloat(item.quantity) || 0;
        const unit = parseFloat(item.unit_price) || 0;
        const amount = qty * unit;

        return `
          <tr>
            <td>${idx + 1}</td>
            <td>${item.description || ""}</td>
            <td class="text-end">${PropertyCalculator.formatCurrency(unit)}</td>
            <td class="text-center">${qty.toFixed(2)}</td>
            <td class="text-end">${PropertyCalculator.formatCurrency(
              amount
            )}</td>
          </tr>
        `;
      })
      .join("");
  }

  prepareProductionPayload() {
    const properties = [];
    this.state.properties.forEach((entry) => {
      if (!entry?.calculation) return;
      properties.push(entry.calculation);
    });

    const addons = this.state.addons?.calculations
      ? Array.from(this.state.addons.calculations.values()).map((a) => ({
          addon_id: a.addonId,
          code: a.code,
          name: a.name,
          amount: a.amount,
          calculation_method: a.calculationMethod,
          display_section: a.displaySection,
        }))
      : [];

    return {
      customer_id: this.state.customer?.id || null,
      warehouse_id: this.state.warehouse?.id || null,
      coil_id: this.state.coil?.id || null,
      stock_entry_id: this.state.stockEntry?.id || null,
      production_paper: {
        customer: this.state.customer,
        warehouse: this.state.warehouse,
        coil: this.state.coil,
        properties: properties,
        addons: addons,
        addonSummary: {
          totalCharges: this.state.addons?.totalCharges || 0,
          totalAdjustments: this.state.addons?.totalAdjustments || 0,
        },
        summary: {
          totalMeters: this.state.productionSummary?.totalMeters || 0,
          totalAmount: this.state.productionSummary?.totalAmount || 0,
        },
        grandTotal:
          this.state.grandTotal ||
          this.state.productionSummary?.totalAmount ||
          0,
      },
    };
  }

  prepareInvoicePayload() {
    const items = [];
    const addonItems = [];
    const combined = this.buildInvoiceItems();

    combined.forEach((item) => {
      if (item.is_addon) addonItems.push(item);
      else items.push(item);
    });

    const taxType = this.state.invoiceData.tax?.type || "fixed";
    const taxValue = this.state.invoiceData.tax?.value || 0;
    const discountType = this.state.invoiceData.discount?.type || "fixed";
    const discountValue = this.state.invoiceData.discount?.value || 0;

    return {
      customer: {
        name: this.state.customer?.name || "",
        company: this.state.customer?.company || "",
        phone: this.state.customer?.phone || "",
        address: this.state.customer?.address || "",
      },
      items: items,
      addon_items: addonItems,
      addon_summary: {
        total_charges: this.state.addons?.totalCharges || 0,
        total_adjustments: this.state.addons?.totalAdjustments || 0,
      },
      tax: this.state.invoiceData.tax?.amount || 0,
      tax_type: taxType,
      tax_value: taxValue,
      tax_rate: taxValue,
      discount: this.state.invoiceData.discount?.amount || 0,
      discount_type: discountType,
      discount_value: discountValue,
      discount_rate: discountValue,
      shipping: this.state.invoiceData.shipping || 0,
      grandTotal: this.state.invoiceData.grandTotal || 0,
      notes: this.state.invoiceData.notes || "",
    };
  }

  renderProductionPreviewHtml(productionPaper) {
    const props = productionPaper?.properties || [];
    const rows = props
      .map((p, idx) => {
        const meters = parseFloat(p.meters) || 0;
        const subtotal = parseFloat(p.subtotal) || 0;
        return `<div>${idx + 1}. ${p.propertyName || ""} - ${meters.toFixed(
          2
        )}m - ${PropertyCalculator.formatCurrency(subtotal)}</div>`;
      })
      .join("");

    return `
      <div><strong>Customer:</strong> ${
        productionPaper?.customer?.name || ""
      }</div>
      <div><strong>Warehouse:</strong> ${
        productionPaper?.warehouse?.name || ""
      }</div>
      <div><strong>Coil:</strong> ${productionPaper?.coil?.name || ""}</div>
      <hr />
      <div><strong>Properties</strong></div>
      ${rows || '<div class="text-muted">No properties</div>'}
      <hr />
      <div><strong>Total Meters:</strong> ${(
        productionPaper?.summary?.totalMeters || 0
      ).toFixed(2)}m</div>
      <div><strong>Total Amount:</strong> ${PropertyCalculator.formatCurrency(
        productionPaper?.summary?.totalAmount || 0
      )}</div>
      <div><strong>Grand Total:</strong> ${PropertyCalculator.formatCurrency(
        productionPaper?.grandTotal ||
          productionPaper?.summary?.totalAmount ||
          0
      )}</div>
    `;
  }

  renderInvoicePreviewHtml(invoicePayload) {
    const rows = (invoicePayload.items || [])
      .concat(invoicePayload.addon_items || [])
      .map((item, idx) => {
        const qty = parseFloat(item.quantity) || 0;
        const unit = parseFloat(item.unit_price) || 0;
        const amount = qty * unit;
        return `<div>${idx + 1}. ${item.description || ""} — ${qty.toFixed(
          2
        )} x ${PropertyCalculator.formatCurrency(
          unit
        )} = ${PropertyCalculator.formatCurrency(amount)}</div>`;
      })
      .join("");

    return `
      <div><strong>Customer:</strong> ${
        invoicePayload.customer?.name || ""
      }</div>
      <hr />
      <div><strong>Items</strong></div>
      ${rows || '<div class="text-muted">No items</div>'}
      <hr />
      <div><strong>Tax:</strong> ${PropertyCalculator.formatCurrency(
        invoicePayload.tax || 0
      )}</div>
      <div><strong>Discount:</strong> -${PropertyCalculator.formatCurrency(
        invoicePayload.discount || 0
      )}</div>
      <div><strong>Shipping:</strong> ${PropertyCalculator.formatCurrency(
        invoicePayload.shipping || 0
      )}</div>
      <div><strong>Total:</strong> ${PropertyCalculator.formatCurrency(
        invoicePayload.grandTotal || 0
      )}</div>
    `;
  }

  /**
   * Get current workflow state for submission
   */
  getWorkflowState() {
    // Convert properties Map to Array
    const propertiesArray = [];
    this.state.properties.forEach((entry, rowId) => {
      if (entry.calculation) {
        propertiesArray.push({
          rowId: rowId,
          ...entry.calculation,
        });
      }
    });

    return {
      customer: this.state.customer,
      warehouse: this.state.warehouse,
      coil: this.state.coil,
      stockEntry: this.state.stockEntry,
      properties: propertiesArray,
      productionSummary: this.state.productionSummary,
      invoiceData: this.state.invoiceData,
    };
  }
  /**
   * EXTENDED Workflow Manager - ADD-ON INTEGRATION
   * File: assets/js/production/workflow-manager.js
   *
   * ADD these methods to the existing WorkflowManager class
   */

  // ============================================================
  // ADD-ON MANAGEMENT METHODS
  // ============================================================

  /**
   * Load add-ons for category
   */
  async loadAddonsForCategory(category) {
    try {
      const response = await fetch(
        `/new-stock-system/controllers/production_properties/get_by_category.php?category=${category}&include_addons=1`
      );

      const data = await response.json();

      if (data.success) {
        // When include_addons=1, backend returns add-ons in `data.addons`
        // (and production properties in `data.properties`). Keep a fallback
        // for older response shapes.
        if (Array.isArray(data.addons)) {
          this.availableAddons = data.addons;
        } else if (Array.isArray(data.all)) {
          this.availableAddons = (data.all || []).filter(
            (p) => p.is_addon == 1
          );
        } else {
          this.availableAddons = (data.properties || []).filter(
            (p) => p.is_addon == 1
          );
        }
        return this.availableAddons;
      } else {
        console.error("Failed to load add-ons:", data.message);
        return [];
      }
    } catch (error) {
      console.error("Error loading add-ons:", error);
      return [];
    }
  }

  /**
   * Initialize add-on state
   */
  initializeAddonState() {
    if (!this.state.addons) {
      this.state.addons = {
        selected: new Map(), // addonId => inputs
        calculations: new Map(), // addonId => result
        totalCharges: 0,
        totalAdjustments: 0,
      };
    }
  }

  /**
   * Show add-on selection UI
   */
  showAddonSelectionUI() {
    this.initializeAddonState();

    const container = document.getElementById("addons_container");
    if (!container) {
      console.warn("Add-ons container not found");
      return;
    }

    // Group add-ons by section
    const addonGroup = this.availableAddons.filter((a) => !a.is_refundable);
    const adjustmentGroup = this.availableAddons.filter((a) => a.is_refundable);

    let html = "";

    // Render add-ons section
    if (addonGroup.length > 0) {
      html += AddonRenderer.renderAddonSection(addonGroup, "addon");
    }

    // Render adjustments section
    if (adjustmentGroup.length > 0) {
      html += AddonRenderer.renderAddonSection(adjustmentGroup, "adjustment");
    }

    container.innerHTML =
      html || '<p class="text-muted">No add-ons available.</p>';

    // Attach event listeners
    AddonRenderer.attachListeners(
      (addon, isChecked) => this.handleAddonToggle(addon, isChecked),
      (addonId) => this.handleAddonInputChange(addonId)
    );

    // Show the add-ons section
    const addonsSection = document.getElementById("addons_section");
    if (addonsSection) {
      addonsSection.classList.remove("d-none");
    }
  }

  /**
   * Handle add-on checkbox toggle
   */
  handleAddonToggle(addon, isChecked) {
    const addonKey = String(addon.id);

    if (isChecked) {
      // Add to selected
      this.state.addons.selected.set(addonKey, {
        customAmount: null,
        quantity: 1,
      });

      // Calculate initial amount
      this.calculateAddon(addonKey);
    } else {
      // Remove from selected
      this.state.addons.selected.delete(addonKey);
      this.state.addons.calculations.delete(addonKey);
    }

    // Recalculate totals
    this.calculateAllAddons();
  }

  /**
   * Handle add-on input change
   */
  handleAddonInputChange(addonId) {
    const addonKey = String(addonId);
    const inputs = this.state.addons.selected.get(addonKey);
    if (!inputs) return;

    // Get current input values
    const customAmountInput = document.querySelector(
      `.addon-custom-amount[data-addon-id="${addonId}"]`
    );
    const quantityInput = document.querySelector(
      `.addon-quantity[data-addon-id="${addonId}"]`
    );

    if (customAmountInput) {
      const raw = customAmountInput.value;
      if (raw === "") {
        inputs.customAmount = null;
      } else {
        const parsed = parseFloat(raw);
        inputs.customAmount = Number.isFinite(parsed) ? parsed : null;
      }
    }

    if (quantityInput) {
      inputs.quantity = parseFloat(quantityInput.value) || 1;
    }

    // Recalculate
    this.calculateAddon(addonKey);
    this.calculateAllAddons();
  }

  /**
   * Calculate single add-on
   */
  calculateAddon(addonId) {
    const addonKey = String(addonId);
    const addon = this.availableAddons.find((a) => a.id == addonKey);
    if (!addon) return;

    const inputs = this.state.addons.selected.get(addonKey);
    if (!inputs) return;

    // Get base amount (production subtotal)
    const baseAmount = this.state.productionSummary.totalAmount;

    // Calculate
    const result = AddonCalculator.calculateAddon(addon, inputs, baseAmount);

    // Store result
    this.state.addons.calculations.set(addonKey, result);

    // Update display
    const amountDisplay = document.getElementById(
      `addon_amount_value_${addonKey}`
    );
    if (amountDisplay) {
      amountDisplay.textContent = PropertyCalculator.formatCurrency(
        result.amount
      );
    }

    return result;
  }

  /**
   * Calculate all add-ons and update totals
   */
  calculateAllAddons() {
    const selectedAddons = [];

    // Collect all selected add-ons with their inputs
    this.state.addons.selected.forEach((inputs, addonId) => {
      const addon = this.availableAddons.find((a) => a.id == addonId);
      if (addon) {
        selectedAddons.push({ addon, inputs });
      }
    });

    // Calculate all
    const subtotal = this.state.productionSummary.totalAmount;
    const results = AddonCalculator.calculateAllAddons(
      selectedAddons,
      subtotal,
      subtotal
    );

    // Update state
    this.state.addons.totalCharges = results.totalAddonCharges;
    this.state.addons.totalAdjustments = results.totalAdjustments;

    // Update UI
    this.updateAddonTotalsDisplay();
    this.updateGrandTotal();
  }

  /**
   * Update add-on totals display
   */
  updateAddonTotalsDisplay() {
    const chargesDisplay = document.getElementById(
      "total_addon_charges_display"
    );
    const adjustmentsDisplay = document.getElementById(
      "total_adjustments_display"
    );

    if (chargesDisplay) {
      chargesDisplay.textContent = PropertyCalculator.formatCurrency(
        this.state.addons.totalCharges
      );
    }

    if (adjustmentsDisplay) {
      adjustmentsDisplay.textContent = PropertyCalculator.formatCurrency(
        this.state.addons.totalAdjustments
      );
    }
  }

  // ============================================================
  // FIX updateGrandTotal method (around line 900)
  // ============================================================
  /**
   * Update grand total (production + add-ons + adjustments)
   */
  updateGrandTotal() {
    const productionTotal = this.state.productionSummary.totalAmount || 0;

    // ✅ FIX: Safely access add-ons state with fallback
    const addonCharges = this.state.addons?.totalCharges || 0;
    const adjustments = this.state.addons?.totalAdjustments || 0;

    const grandTotal = productionTotal + addonCharges + adjustments;

    const grandTotalDisplay = document.getElementById("grand_total_display");
    if (grandTotalDisplay) {
      grandTotalDisplay.textContent =
        PropertyCalculator.formatCurrency(grandTotal);
    }

    // Also update production total display
    const productionTotalDisplay = document.getElementById(
      "production_total_display"
    );
    if (productionTotalDisplay) {
      productionTotalDisplay.textContent =
        PropertyCalculator.formatCurrency(productionTotal);
    }

    // Update add-ons display
    const addonsTotalDisplay = document.getElementById("addons_total_display");
    if (addonsTotalDisplay) {
      addonsTotalDisplay.textContent = PropertyCalculator.formatCurrency(
        addonCharges + adjustments
      );
    }

    // Update production count
    const productionCountDisplay = document.getElementById("production_count");
    if (productionCountDisplay) {
      productionCountDisplay.textContent = this.state.properties.size;
    }

    // Update state
    this.state.grandTotal = grandTotal;
  }

  /**
   * Get add-ons for invoice generation
   */
  getAddonsForInvoice() {
    const addonItems = [];

    this.state.addons.calculations.forEach((result) => {
      addonItems.push({
        description: result.name,
        quantity: 1,
        qty_text:
          result.calculationMethod === "percentage"
            ? `${result.defaultPrice}%`
            : "1 item",
        unit_price: result.amount,
        subtotal: result.amount,
        is_addon: true,
        addon_id: result.addonId,
        display_section: result.displaySection,
      });
    });

    return addonItems;
  }

  /**
   * OVERRIDE: Get workflow state for submission (add add-ons)
   */
  getWorkflowState() {
    // Convert properties Map to Array
    const propertiesArray = [];
    this.state.properties.forEach((entry, rowId) => {
      if (entry.calculation) {
        propertiesArray.push({
          rowId: rowId,
          ...entry.calculation,
        });
      }
    });

    // Get add-ons
    const addonsArray = this.getAddonsForInvoice();

    return {
      customer: this.state.customer,
      warehouse: this.state.warehouse,
      coil: this.state.coil,
      stockEntry: this.state.stockEntry,
      properties: propertiesArray,
      addons: addonsArray, // NEW
      productionSummary: this.state.productionSummary,
      addonSummary: {
        // NEW
        totalCharges: this.state.addons.totalCharges,
        totalAdjustments: this.state.addons.totalAdjustments,
      },
      grandTotal: this.state.grandTotal, // NEW
      invoiceData: this.state.invoiceData,
    };
  }

  /**
   * OVERRIDE: Validate production tab (include add-ons check)
   */
  validateProductionTab() {
    const hasCustomer = !!this.state.customer;
    const hasWarehouse = !!this.state.warehouse;
    const hasCoil = !!this.state.coil;
    const hasStockEntry = !!this.state.stockEntry;
    const hasProperties = this.state.properties.size > 0;
    const hasValidAmount = this.state.productionSummary?.totalAmount > 0;

    // Add-ons are optional, so they don't affect validation

    const isValid =
      hasCustomer &&
      hasWarehouse &&
      hasCoil &&
      hasStockEntry &&
      hasProperties &&
      hasValidAmount;

    const btn = document.getElementById("proceed_to_invoice_btn");
    if (btn) {
      btn.disabled = !isValid;
    }

    return isValid;
  }

  /**
   * OVERRIDE: Calculate production totals (trigger add-on recalc)
   */
  calculateProductionTotals() {
    let totalMeters = 0;
    let totalAmount = 0;

    this.state.properties.forEach((entry) => {
      if (entry.calculation) {
        totalMeters += parseFloat(entry.calculation.meters) || 0;
        totalAmount += parseFloat(entry.calculation.subtotal) || 0;
      }
    });

    this.state.productionSummary = { totalMeters, totalAmount };

    // Update UI
    const metersDisplay = document.getElementById("total_meters_display");
    const amountDisplay = document.getElementById("production_total_display");

    if (metersDisplay) {
      metersDisplay.textContent =
        totalMeters > 0 ? totalMeters.toFixed(2) + "m" : "N/A";
    }

    if (amountDisplay) {
      amountDisplay.textContent =
        PropertyCalculator.formatCurrency(totalAmount);
    }

    // Show/hide summary box
    const summaryBox = document.getElementById("production_summary");
    if (summaryBox) {
      if (this.state.properties.size > 0) {
        summaryBox.classList.remove("d-none");
      } else {
        summaryBox.classList.add("d-none");
      }
    }

    // Recalculate add-ons if any are selected
    if (this.state.addons && this.state.addons.selected.size > 0) {
      this.calculateAllAddons();
    } else {
      this.updateGrandTotal();
    }

    this.validateProductionTab();
  }

  /**
   * Set stock entry
   */
  setStockEntry(stockEntryData) {
    this.state.stockEntry = stockEntryData;
    console.log("✅ Stock entry set:", stockEntryData);
    this.validateProductionTab();
  }

  /**
   * Set customer
   */
  setCustomer(customerData) {
    this.state.customer = customerData;
    this.updateSelectionSummary();
    this.validateProductionTab();
  }

  /**
   * Set warehouse
   */
  setWarehouse(warehouseData) {
    this.state.warehouse = warehouseData;
    this.updateSelectionSummary();
    this.validateProductionTab();
  }

  /**
   * Update selection summary display
   */
  updateSelectionSummary() {
    const summaryEl = document.getElementById("selection_summary");
    const textEl = document.getElementById("selection_text");

    if (!summaryEl || !textEl) return;

    if (this.state.customer && this.state.warehouse) {
      textEl.textContent = `Customer: ${this.state.customer.name} | Warehouse: ${this.state.warehouse.name}`;
      summaryEl.classList.remove("d-none");
    } else {
      summaryEl.classList.add("d-none");
    }
  }
}

// Create and expose global instance
window.workflowManager = new WorkflowManager();

// For CommonJS environments (Node.js)
if (typeof module !== "undefined" && module.exports) {
  module.exports = {
    WorkflowManager,
    workflowManager: window.workflowManager,
  };
}
