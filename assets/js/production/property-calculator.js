/**
 * Property Calculator Module
 * File: assets/js/production/property-calculator.js
 *
 * Handles all calculation logic for different property types
 */

class PropertyCalculator {
  /**
   * Calculate based on property type
   *
   * @param {Object} property - Property configuration
   * @param {Object} inputs - User inputs
   * @returns {Object} Calculation results
   */
  static calculate(property, inputs) {
    switch (property.property_type) {
      case "meter_based":
        return this.calculateMeterBased(property, inputs);
      case "unit_based":
        return this.calculateUnitBased(property, inputs);
      case "bundle_based":
        return this.calculateBundleBased(property, inputs);
      default:
        console.error("Unknown property type:", property.property_type);
        return {
          quantity: 0,
          meters: 0,
          pieces: 0,
          subtotal: 0,
          error: "Unknown property type",
        };
    }
  }

  /**
   * Calculate meter-based property (Alusteel/Aluminum)
   * Formula: meters = sheetQty × sheetMeter
   */
  static calculateMeterBased(property, inputs) {
    const sheetQty = parseFloat(inputs.sheetQty) || 0;
    const sheetMeter = parseFloat(inputs.sheetMeter) || 0;
    const inputUnitPrice = parseFloat(inputs.unitPrice);
    const unitPrice = Number.isFinite(inputUnitPrice)
      ? inputUnitPrice
      : parseFloat(property.default_price) || 0;

    const meters = sheetQty * sheetMeter;
    const subtotal = meters * unitPrice;

    return {
      propertyType: property.code,
      propertyName: property.name,
      sheetQty: sheetQty,
      sheetMeter: sheetMeter,
      quantity: sheetQty, // For invoice display
      meters: meters,
      pieces: 0,
      unitPrice: unitPrice,
      subtotal: subtotal,
    };
  }

  /**
   * Calculate unit-based property (KZINC Scraps/Pieces)
   * Formula: subtotal = quantity × unitPrice
   */
  static calculateUnitBased(property, inputs) {
    const quantity = parseFloat(inputs.quantity) || 0;
    const inputUnitPrice = parseFloat(inputs.unitPrice);
    const unitPrice = Number.isFinite(inputUnitPrice)
      ? inputUnitPrice
      : parseFloat(property.default_price) || 0;

    const subtotal = quantity * unitPrice;

    return {
      propertyType: property.code,
      propertyName: property.name,
      quantity: quantity,
      meters: 0,
      pieces: quantity, // For unit-based, pieces = quantity
      unitPrice: unitPrice,
      subtotal: subtotal,
    };
  }

  /**
   * Calculate bundle-based property (KZINC Bundles)
   * Formula: pieces = bundles × piecesPerBundle
   */
  static calculateBundleBased(property, inputs) {
    const bundles = parseFloat(inputs.quantity) || 0;
    const inputUnitPrice = parseFloat(inputs.unitPrice);
    const unitPrice = Number.isFinite(inputUnitPrice)
      ? inputUnitPrice
      : parseFloat(property.default_price) || 0;

    // Get pieces per bundle from property metadata
    const piecesPerBundle = property.metadata?.pieces_per_bundle || 15;
    const totalPieces = bundles * piecesPerBundle;

    const subtotal = bundles * unitPrice;

    return {
      propertyType: property.code,
      propertyName: property.name,
      quantity: bundles,
      meters: 0,
      pieces: totalPieces,
      unitPrice: unitPrice,
      subtotal: subtotal,
      piecesPerBundle: piecesPerBundle,
    };
  }

  /**
   * Format currency for display
   */
  static formatCurrency(amount) {
    return (
      "₦" +
      parseFloat(amount || 0).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
  }

  /**
   * Validate inputs before calculation
   */
  static validateInputs(property, inputs) {
    const errors = [];

    switch (property.property_type) {
      case "meter_based":
        if (!inputs.sheetQty || inputs.sheetQty <= 0) {
          errors.push("Sheet quantity must be greater than 0");
        }
        if (!inputs.sheetMeter || inputs.sheetMeter <= 0) {
          errors.push("Meter per sheet must be greater than 0");
        }
        break;

      case "unit_based":
      case "bundle_based":
        if (!inputs.quantity || inputs.quantity <= 0) {
          errors.push("Quantity must be greater than 0");
        }
        break;
    }

    if (
      inputs.unitPrice !== undefined &&
      inputs.unitPrice !== null &&
      inputs.unitPrice !== ""
    ) {
      const unitPrice = parseFloat(inputs.unitPrice);
      if (isNaN(unitPrice)) {
        errors.push("Invalid unit price format");
      } else if (unitPrice < 0) {
        errors.push("Unit price cannot be negative");
      }
    }

    return {
      valid: errors.length === 0,
      errors: errors,
    };
  }
}

// Export for use in other modules
if (typeof module !== "undefined" && module.exports) {
  module.exports = PropertyCalculator;
}
