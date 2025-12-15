/**
 * Add-On Calculator Extension
 * File: assets/js/production/addon-calculator.js
 *
 * Extends PropertyCalculator to handle add-on calculations
 */

class AddonCalculator extends PropertyCalculator {
  /**
   * Calculate add-on amount based on method
   *
   * @param {Object} addon - Add-on property configuration
   * @param {Object} inputs - User inputs
   * @param {float} baseAmount - Base amount to calculate from (subtotal or total)
   * @returns {Object} Calculation results
   */
  static calculateAddon(addon, inputs, baseAmount = 0) {
    const hasCustomAmount =
      inputs.customAmount !== undefined &&
      inputs.customAmount !== null &&
      inputs.customAmount !== "";
    const customAmount = hasCustomAmount
      ? parseFloat(inputs.customAmount)
      : NaN;
    const defaultPrice = parseFloat(addon.default_price) || 0;
    const calculationMethod = addon.calculation_method || "fixed";
    const isRefund = addon.is_refundable == 1;

    let amount = 0;

    // If custom amount provided, use it (including 0)
    if (hasCustomAmount && !isNaN(customAmount)) {
      amount = customAmount;
    } else {
      // Calculate based on method
      switch (calculationMethod) {
        case "fixed":
          amount = defaultPrice;
          break;

        case "percentage":
          amount = (baseAmount * defaultPrice) / 100;
          break;

        case "per_unit":
          const quantity = parseFloat(inputs.quantity) || 1;
          amount = defaultPrice * quantity;
          break;

        default:
          amount = defaultPrice;
      }
    }

    // Make negative if it's a refund
    if (isRefund && amount > 0) {
      amount = -amount;
    }

    return {
      addonId: addon.id,
      code: addon.code,
      name: addon.name,
      calculationMethod: calculationMethod,
      defaultPrice: defaultPrice,
      customAmount:
        hasCustomAmount && !isNaN(customAmount) ? customAmount : null,
      baseAmount: baseAmount,
      amount: amount,
      isRefund: isRefund,
      displaySection: addon.display_section,
    };
  }

  /**
   * Calculate all add-ons for a sale
   *
   * @param {Array} selectedAddons - Array of selected add-ons with inputs
   * @param {float} subtotal - Production items subtotal
   * @param {float} total - Total after tax (if applicable)
   * @returns {Object} Summary with amounts grouped by section
   */
  static calculateAllAddons(selectedAddons, subtotal = 0, total = 0) {
    const results = {
      items: [],
      totalAddonCharges: 0,
      totalAdjustments: 0,
      grandTotal: 0,
    };

    selectedAddons.forEach((item) => {
      const { addon, inputs } = item;

      // Determine base amount based on applies_to
      const baseAmount = addon.applies_to === "subtotal" ? subtotal : total;

      // Calculate
      const result = this.calculateAddon(addon, inputs, baseAmount);
      results.items.push(result);

      // Add to appropriate total
      if (result.isRefund || result.amount < 0) {
        results.totalAdjustments += result.amount;
      } else {
        results.totalAddonCharges += result.amount;
      }
    });

    // Calculate grand total
    results.grandTotal =
      subtotal + results.totalAddonCharges + results.totalAdjustments;

    return results;
  }

  /**
   * Validate add-on inputs
   *
   * @param {Object} addon - Add-on configuration
   * @param {Object} inputs - User inputs
   * @returns {Object} Validation result
   */
  static validateAddonInputs(addon, inputs) {
    const errors = [];

    // Check custom amount if provided
    if (inputs.customAmount !== undefined && inputs.customAmount !== "") {
      const amount = parseFloat(inputs.customAmount);
      if (isNaN(amount)) {
        errors.push("Invalid amount format");
      }
      if (amount < 0 && !addon.is_refundable) {
        errors.push("Amount cannot be negative for non-refund items");
      }
    }

    // Check quantity for per_unit calculation
    if (
      addon.calculation_method === "per_unit" &&
      inputs.quantity !== undefined
    ) {
      const quantity = parseFloat(inputs.quantity);
      if (isNaN(quantity) || quantity <= 0) {
        errors.push("Quantity must be greater than 0");
      }
    }

    return {
      valid: errors.length === 0,
      errors: errors,
    };
  }

  /**
   * Format add-on for display
   *
   * @param {Object} result - Calculation result
   * @returns {string} Formatted display string
   */
  static formatAddonDisplay(result) {
    let display = result.name;

    if (result.calculationMethod === "percentage") {
      display += ` (${result.defaultPrice}%)`;
    }

    if (result.customAmount !== null) {
      display += " [Custom]";
    }

    return display;
  }

  /**
   * Group add-ons by display section
   *
   * @param {Array} addonResults - Array of calculation results
   * @returns {Object} Grouped add-ons
   */
  static groupBySection(addonResults) {
    const grouped = {
      addon: [],
      adjustment: [],
    };

    addonResults.forEach((result) => {
      const section = result.displaySection || "addon";
      if (section === "adjustment") {
        grouped.adjustment.push(result);
      } else {
        grouped.addon.push(result);
      }
    });

    return grouped;
  }
}

// Export for use in other modules
if (typeof module !== "undefined" && module.exports) {
  module.exports = AddonCalculator;
}
