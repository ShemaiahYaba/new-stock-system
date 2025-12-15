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
    this.currentCategory = null;
  }

  /**
   * Handle coil selection
   * @param {Object} coilData - The selected coil data
   */
  async handleCoilSelection(coilData) {
    try {
      // Update the state with the selected coil
      this.state.coil = coilData;

      // Clear any existing stock entry
      this.state.stockEntry = null;
      document.getElementById("stock_entry_id").value = "";

      // Load stock entries for the selected coil
      const response = await fetch(
        `/new-stock-system/controllers/sales/get_stock_entries.php?coil_id=${coilData.id}`
      );

      const data = await response.json();

      if (data.success) {
        const stockSelect = document.getElementById("stock_entry_id");
        stockSelect.innerHTML = '<option value="">Select Stock Entry</option>';

        data.stockEntries.forEach((entry) => {
          const option = document.createElement("option");
          option.value = entry.id;
          option.textContent = `#${entry.id} - ${entry.meters_remaining}m (${entry.status})`;
          option.dataset.status = entry.status;
          option.dataset.meters = entry.meters_remaining;
          stockSelect.appendChild(option);
        });

        stockSelect.disabled = false;
        document.getElementById("properties_container").classList.add("d-none");
        this.resetProperties();
      } else {
        throw new Error(data.message || "Failed to load stock entries");
      }
    } catch (error) {
      console.error("Error handling coil selection:", error);
      alert("Error: " + error.message);
    }
  }

  /**
   * Reset all properties and UI elements
   */
  resetProperties() {
    this.state.properties.clear();
    this.rowCounter = 0;
    document.getElementById("property_rows").innerHTML = "";
    this.updateProductionSummary();
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

    // Collect inputs based on property type
    const inputs = this.collectRowInputs(row, property.property_type);

    // Validate inputs
    const validation = PropertyCalculator.validateInputs(property, inputs);
    if (!validation.valid) {
      console.warn("Validation errors:", validation.errors);
      return;
    }

    // Calculate
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
        inputs.unitPrice =
          parseFloat(row.querySelector(".unit-price")?.value) || 0;
        break;

      case "unit_based":
      case "bundle_based":
        inputs.quantity =
          parseFloat(row.querySelector(".kzinc-quantity")?.value) || 0;
        inputs.unitPrice =
          parseFloat(row.querySelector(".kzinc-unit-price")?.value) || 0;
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
    const amountDisplay = document.getElementById("total_amount_display");

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
    if (this.currentCategory === "kzinc") {
      return document.getElementById("kzinc_properties_container");
    } else {
      return document.getElementById("properties_container");
    }
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
        // Separate production properties from add-ons
        this.availableAddons = (data.properties || []).filter(
          (p) => p.is_addon == 1
        );
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
    if (isChecked) {
      // Add to selected
      this.state.addons.selected.set(addon.id, {
        customAmount: null,
        quantity: 1,
      });

      // Calculate initial amount
      this.calculateAddon(addon.id);
    } else {
      // Remove from selected
      this.state.addons.selected.delete(addon.id);
      this.state.addons.calculations.delete(addon.id);
    }

    // Recalculate totals
    this.calculateAllAddons();
  }

  /**
   * Handle add-on input change
   */
  handleAddonInputChange(addonId) {
    const inputs = this.state.addons.selected.get(addonId);
    if (!inputs) return;

    // Get current input values
    const customAmountInput = document.querySelector(
      `.addon-custom-amount[data-addon-id="${addonId}"]`
    );
    const quantityInput = document.querySelector(
      `.addon-quantity[data-addon-id="${addonId}"]`
    );

    if (customAmountInput) {
      inputs.customAmount = parseFloat(customAmountInput.value) || null;
    }

    if (quantityInput) {
      inputs.quantity = parseFloat(quantityInput.value) || 1;
    }

    // Recalculate
    this.calculateAddon(addonId);
    this.calculateAllAddons();
  }

  /**
   * Calculate single add-on
   */
  calculateAddon(addonId) {
    const addon = this.availableAddons.find((a) => a.id == addonId);
    if (!addon) return;

    const inputs = this.state.addons.selected.get(addonId);
    if (!inputs) return;

    // Get base amount (production subtotal)
    const baseAmount = this.state.productionSummary.totalAmount;

    // Calculate
    const result = AddonCalculator.calculateAddon(addon, inputs, baseAmount);

    // Store result
    this.state.addons.calculations.set(addonId, result);

    // Update display
    const amountDisplay = document.getElementById(
      `addon_amount_value_${addonId}`
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

  /**
   * Update grand total (production + add-ons + adjustments)
   */
  updateGrandTotal() {
    const productionTotal = this.state.productionSummary.totalAmount;
    const addonCharges = this.state.addons.totalCharges;
    const adjustments = this.state.addons.totalAdjustments;

    const grandTotal = productionTotal + addonCharges + adjustments;

    const grandTotalDisplay = document.getElementById("grand_total_display");
    if (grandTotalDisplay) {
      grandTotalDisplay.textContent =
        PropertyCalculator.formatCurrency(grandTotal);
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
    const amountDisplay = document.getElementById("total_amount_display");

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
