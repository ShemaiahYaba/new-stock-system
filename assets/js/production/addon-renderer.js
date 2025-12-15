/**
 * Add-On Renderer Extension
 * File: assets/js/production/addon-renderer.js
 *
 * Renders add-on selection and input UI
 */

class AddonRenderer {
  /**
   * Render add-on selection section
   *
   * @param {Array} addons - Available add-ons
   * @param {string} sectionType - 'addon' or 'adjustment'
   * @returns {string} HTML string
   */
  static renderAddonSection(addons, sectionType = "addon") {
    if (!addons || addons.length === 0) {
      return '<p class="text-muted">No add-ons available for this category.</p>';
    }

    const sectionTitle =
      sectionType === "addon" ? "Add-On Charges" : "Adjustments & Refunds";
    const sectionIcon =
      sectionType === "addon" ? "bi-plus-circle" : "bi-dash-circle";
    const sectionColor = sectionType === "addon" ? "success" : "warning";

    let html = `
            <div class="addon-section mb-4" data-section="${sectionType}">
                <h5 class="mb-3">
                    <i class="bi ${sectionIcon} text-${sectionColor}"></i> ${sectionTitle}
                </h5>
                <div class="row">
        `;

    addons.forEach((addon) => {
      html += this.renderAddonCard(addon, sectionType);
    });

    html += `
                </div>
            </div>
        `;

    return html;
  }

  /**
   * Render individual add-on card
   *
   * @param {Object} addon - Add-on property
   * @param {string} sectionType - Section identifier
   * @returns {string} HTML string
   */
  static renderAddonCard(addon, sectionType) {
    const isRefund = addon.is_refundable == 1;
    const cardClass = isRefund ? "border-warning" : "border-success";
    const defaultPriceDisplay = this.formatAddonPrice(addon);

    return `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card addon-card ${cardClass}" data-addon-id="${
      addon.id
    }">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input addon-checkbox" 
                                   type="checkbox" 
                                   id="addon_${addon.id}" 
                                   data-addon-id="${addon.id}"
                                   data-addon-data='${JSON.stringify(addon)}'>
                            <label class="form-check-label w-100" for="addon_${
                              addon.id
                            }">
                                <strong>${addon.name}</strong>
                                ${
                                  isRefund
                                    ? '<span class="badge bg-warning text-dark ms-1">Refund</span>'
                                    : ""
                                }
                            </label>
                        </div>
                        
                        <div class="addon-details mt-2">
                            <small class="text-muted d-block">
                                ${this.getCalculationDescription(addon)}
                            </small>
                            <div class="mt-2">
                                <small class="text-muted">Default:</small>
                                <strong class="text-primary">${defaultPriceDisplay}</strong>
                            </div>
                        </div>
                        
                        <!-- Custom Amount Input (hidden by default) -->
                        <div class="addon-input mt-3 d-none" id="addon_input_${
                          addon.id
                        }">
                            ${this.renderAddonInput(addon)}
                        </div>
                        
                        <!-- Calculated Amount Display -->
                        <div class="addon-amount mt-2 d-none" id="addon_amount_${
                          addon.id
                        }">
                            <div class="alert alert-info py-2 px-3 mb-0">
                                <small>Amount:</small>
                                <strong id="addon_amount_value_${
                                  addon.id
                                }">₦0.00</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
  }

  /**
   * Render input field for add-on
   *
   * @param {Object} addon - Add-on property
   * @returns {string} HTML string
   */
  static renderAddonInput(addon) {
    const calculationMethod = addon.calculation_method || "fixed";
    const isPerUnit = calculationMethod === "per_unit";

    let html = "";

    // Quantity input for per_unit
    if (isPerUnit) {
      html += `
                <div class="mb-2">
                    <label class="form-label small">Quantity</label>
                    <input type="number" 
                           class="form-control form-control-sm addon-quantity" 
                           data-addon-id="${addon.id}"
                           min="1" 
                           step="1" 
                           value="1"
                           placeholder="Enter quantity">
                </div>
            `;
    }

    // Custom amount input
    html += `
            <div class="mb-2">
                <label class="form-label small">Custom Amount (₦)</label>
                <input type="number" 
                       class="form-control form-control-sm addon-custom-amount" 
                       data-addon-id="${addon.id}"
                       min="0" 
                       step="0.01" 
                       placeholder="${addon.default_price || "0.00"}">
                <small class="form-text text-muted">Leave blank to use default</small>
            </div>
        `;

    return html;
  }

  /**
   * Format add-on price for display
   *
   * @param {Object} addon - Add-on property
   * @returns {string} Formatted price
   */
  static formatAddonPrice(addon) {
    const price = parseFloat(addon.default_price) || 0;
    const method = addon.calculation_method || "fixed";

    if (method === "percentage") {
      return `${price.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}%`;
    } else if (method === "per_unit") {
      return `${PropertyCalculator.formatCurrency(price)} per unit`;
    } else {
      return PropertyCalculator.formatCurrency(price);
    }
  }

  /**
   * Get calculation description
   *
   * @param {Object} addon - Add-on property
   * @returns {string} Description text
   */
  static getCalculationDescription(addon) {
    const method = addon.calculation_method || "fixed";
    const appliesTo = addon.applies_to || "total";
    const price = parseFloat(addon.default_price) || 0;

    const descriptions = {
      fixed: "Fixed amount",
      percentage: `${price.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}% of ${appliesTo}`,
      per_unit: "Calculated per unit",
    };

    return descriptions[method] || "Fixed amount";
  }

  /**
   * Render add-on summary for invoice preview
   *
   * @param {Array} addonResults - Calculated add-on results
   * @returns {string} HTML string
   */
  static renderAddonSummary(addonResults) {
    if (!addonResults || addonResults.length === 0) {
      return "";
    }

    const grouped = AddonCalculator.groupBySection(addonResults);
    let html = "";

    // Add-On Charges Section
    if (grouped.addon.length > 0) {
      html += `
                <tr class="table-secondary">
                    <td colspan="4"><strong>Add-On Charges</strong></td>
                    <td></td>
                </tr>
            `;

      grouped.addon.forEach((result) => {
        html += `
                    <tr>
                        <td colspan="4" class="ps-4">${AddonCalculator.formatAddonDisplay(
                          result
                        )}</td>
                        <td class="text-end">${PropertyCalculator.formatCurrency(
                          result.amount
                        )}</td>
                    </tr>
                `;
      });
    }

    // Adjustments Section
    if (grouped.adjustment.length > 0) {
      html += `
                <tr class="table-warning">
                    <td colspan="4"><strong>Adjustments</strong></td>
                    <td></td>
                </tr>
            `;

      grouped.adjustment.forEach((result) => {
        html += `
                    <tr>
                        <td colspan="4" class="ps-4">${AddonCalculator.formatAddonDisplay(
                          result
                        )}</td>
                        <td class="text-end">${PropertyCalculator.formatCurrency(
                          result.amount
                        )}</td>
                    </tr>
                `;
      });
    }

    return html;
  }

  /**
   * Render add-on invoice items for final submission
   *
   * @param {Array} addonResults - Calculated add-on results
   * @returns {Array} Invoice items array
   */
  static formatAddonsForInvoice(addonResults) {
    return addonResults.map((result) => ({
      description: result.name,
      quantity: 1,
      qty_text:
        result.calculationMethod === "percentage"
          ? `${result.defaultPrice}%`
          : "1 item",
      unit_price: result.amount,
      subtotal: result.amount,
      is_addon: true,
      display_section: result.displaySection,
    }));
  }

  /**
   * Attach event listeners to add-on checkboxes
   *
   * @param {Function} onToggle - Callback when checkbox is toggled
   * @param {Function} onChange - Callback when input values change
   */
  static attachListeners(onToggle, onChange) {
    // Checkbox toggle
    document.querySelectorAll(".addon-checkbox").forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        const addonId = this.dataset.addonId;
        const isChecked = this.checked;
        const addon = JSON.parse(this.dataset.addonData);

        // Show/hide input fields
        const inputContainer = document.getElementById(
          `addon_input_${addonId}`
        );
        const amountContainer = document.getElementById(
          `addon_amount_${addonId}`
        );

        if (isChecked) {
          inputContainer?.classList.remove("d-none");
          amountContainer?.classList.remove("d-none");
        } else {
          inputContainer?.classList.add("d-none");
          amountContainer?.classList.add("d-none");
        }

        // Trigger callback
        if (onToggle) {
          onToggle(addon, isChecked);
        }
      });
    });

    // Input changes
    document
      .querySelectorAll(".addon-custom-amount, .addon-quantity")
      .forEach((input) => {
        input.addEventListener("input", function () {
          const addonId = this.dataset.addonId;

          if (onChange) {
            onChange(addonId);
          }
        });
      });
  }
}

// Export for use in other modules
if (typeof module !== "undefined" && module.exports) {
  module.exports = AddonRenderer;
}
