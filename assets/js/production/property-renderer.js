/**
 * Property Renderer Module
 * File: assets/js/production/property-renderer.js
 *
 * Handles dynamic HTML rendering for different property types
 */

class PropertyRenderer {
  /**
   * Render a property row based on property type
   *
   * @param {Object} property - Property configuration
   * @param {number} rowId - Unique row identifier
   * @returns {string} HTML string
   */
  static renderRow(property, rowId) {
    switch (property.property_type) {
      case "meter_based":
        return this.renderMeterBasedRow(property, rowId);
      case "unit_based":
        return this.renderUnitBasedRow(property, rowId);
      case "bundle_based":
        return this.renderBundleBasedRow(property, rowId);
      default:
        console.error("Unknown property type:", property.property_type);
        return "";
    }
  }

  /**
   * Render meter-based property row (Alusteel/Aluminum)
   */
  static renderMeterBasedRow(property, rowId) {
    return `
            <div class="property-row" id="property_row_${rowId}" data-property-id="${
      property.id
    }">
                <div class="property-row-header d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <span class="badge bg-primary">${property.name}</span>
                        <small class="text-muted ms-2">${property.code}</small>
                    </h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="workflowManager.removePropertyRow(${rowId})">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Sheet Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control sheet-qty" data-row-id="${rowId}" 
                               min="1" step="1" placeholder="e.g., 24" required>
                    </div>
                    
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Meter/Sheet <span class="text-danger">*</span></label>
                        <input type="number" class="form-control sheet-meter" data-row-id="${rowId}" 
                               min="0.01" step="0.01" placeholder="e.g., 8.20" required>
                    </div>
                    
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Total Meters</label>
                        <input type="text" class="form-control bg-light computed-meters" id="meters_${rowId}" readonly>
                    </div>
                    
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Unit Price (₦)</label>
                        <input type="number" class="form-control unit-price" data-row-id="${rowId}" 
                               min="0" step="0.01" placeholder="${
                                 property.default_price || "0.00"
                               }" 
                               value="${property.default_price || ""}">
                    </div>
                    
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control bg-light" id="subtotal_${rowId}" readonly>
                    </div>
                </div>
                
                <input type="hidden" class="property-type" value="${
                  property.property_type
                }">
                <input type="hidden" class="property-code" value="${
                  property.code
                }">
                <input type="hidden" class="property-name" value="${
                  property.name
                }">
            </div>
        `;
  }

  /**
   * Render unit-based property row (KZINC Scraps/Pieces)
   */
  static renderUnitBasedRow(property, rowId) {
    return `
            <div class="property-row" id="property_row_${rowId}" data-property-id="${
      property.id
    }">
                <div class="property-row-header d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <span class="badge bg-success">${property.name}</span>
                        <small class="text-muted ms-2">${property.code}</small>
                    </h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="workflowManager.removePropertyRow(${rowId})">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control kzinc-quantity" data-row-id="${rowId}" 
                               min="1" step="1" placeholder="e.g., 10" required>
                        <small class="text-muted">Number of ${property.name.toLowerCase()}</small>
                    </div>
                    
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Unit Price (₦) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control kzinc-unit-price" data-row-id="${rowId}" 
                               min="0" step="0.01" value="${
                                 property.default_price || ""
                               }" required>
                        <small class="text-muted">Default: ₦${parseFloat(
                          property.default_price || 0
                        ).toLocaleString()}</small>
                    </div>
                    
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Subtotal (₦)</label>
                        <input type="text" class="form-control bg-light" id="kzinc_subtotal_${rowId}" readonly>
                    </div>
                </div>
                
                <input type="hidden" class="property-type" value="${
                  property.property_type
                }">
                <input type="hidden" class="property-code" value="${
                  property.code
                }">
                <input type="hidden" class="property-name" value="${
                  property.name
                }">
            </div>
        `;
  }

  /**
   * Render bundle-based property row (KZINC Bundles)
   */
  static renderBundleBasedRow(property, rowId) {
    const piecesPerBundle = property.metadata?.pieces_per_bundle || 15;

    return `
            <div class="property-row" id="property_row_${rowId}" data-property-id="${
      property.id
    }">
                <div class="property-row-header d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <span class="badge bg-warning text-dark">${
                          property.name
                        }</span>
                        <small class="text-muted ms-2">${
                          property.code
                        } - ${piecesPerBundle} pieces/bundle</small>
                    </h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="workflowManager.removePropertyRow(${rowId})">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Bundles <span class="text-danger">*</span></label>
                        <input type="number" class="form-control kzinc-quantity" data-row-id="${rowId}" 
                               min="1" step="1" placeholder="e.g., 5" required>
                    </div>
                    
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Total Pieces</label>
                        <input type="text" class="form-control bg-light" id="pieces_value_${rowId}" readonly>
                        <small class="text-muted">Auto-calculated</small>
                    </div>
                    
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Unit Price (₦) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control kzinc-unit-price" data-row-id="${rowId}" 
                               min="0" step="0.01" value="${
                                 property.default_price || ""
                               }" required>
                        <small class="text-muted">Per bundle</small>
                    </div>
                    
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Subtotal (₦)</label>
                        <input type="text" class="form-control bg-light" id="kzinc_subtotal_${rowId}" readonly>
                    </div>
                </div>
                
                <input type="hidden" class="property-type" value="${
                  property.property_type
                }">
                <input type="hidden" class="property-code" value="${
                  property.code
                }">
                <input type="hidden" class="property-name" value="${
                  property.name
                }">
                <input type="hidden" class="pieces-per-bundle" value="${piecesPerBundle}">
            </div>
        `;
  }

  /**
   * Attach event listeners to a property row
   *
   * @param {number} rowId - Row identifier
   * @param {Object} property - Property configuration
   */
  static attachListeners(rowId, property) {
    const row = document.getElementById(`property_row_${rowId}`);
    if (!row) return;

    const propertyType = property.property_type;

    // Get all input fields
    const inputs = row.querySelectorAll('input[type="number"]');

    // Attach change listeners
    inputs.forEach((input) => {
      input.addEventListener("input", () => {
        workflowManager.calculatePropertyRow(rowId, property);
      });
    });
  }

  /**
   * Create property dropdown for selection
   *
   * @param {Array} properties - Array of property objects
   * @returns {string} HTML string for dropdown
   */
  static renderPropertyDropdown(properties) {
    if (!properties || properties.length === 0) {
      return '<option value="">No properties available</option>';
    }

    let html = '<option value="">-- Select Property --</option>';

    properties.forEach((prop) => {
      const icon = this.getPropertyTypeIcon(prop.property_type);
      html += `<option value="${prop.id}" data-property='${JSON.stringify(
        prop
      )}'>
                ${icon} ${prop.name} (${prop.code}) - ₦${parseFloat(
        prop.default_price || 0
      ).toLocaleString()}
            </option>`;
    });

    return html;
  }

  /**
   * Get icon for property type
   */
  static getPropertyTypeIcon(propertyType) {
    const icons = {
      meter_based: "📏",
      unit_based: "📦",
      bundle_based: "📚",
    };
    return icons[propertyType] || "❓";
  }
}

// Export for use in other modules
if (typeof module !== "undefined" && module.exports) {
  module.exports = PropertyRenderer;
}
