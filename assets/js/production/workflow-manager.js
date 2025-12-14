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
}

// Create global instance
const workflowManager = new WorkflowManager();

// Export for use in other modules
if (typeof module !== "undefined" && module.exports) {
  module.exports = WorkflowManager;
}
