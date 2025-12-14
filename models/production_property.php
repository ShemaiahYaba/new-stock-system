<?php
/**
 * ProductionProperty Model
 * File: models/production_property.php
 * 
 * Handles all production property CRUD operations
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class ProductionProperty {
    private $db;
    private $table = 'production_properties';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
   /**
 * Update existing create method to support new fields
 * REPLACE the existing create() method with this version
 */
public function create($data) {
    try {
        $sql = "INSERT INTO {$this->table} 
                (code, name, category, property_type, is_addon, calculation_method, 
                 applies_to, is_refundable, display_section, default_price, 
                 sort_order, metadata, is_active, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['code'],
            $data['name'],
            $data['category'],
            $data['property_type'],
            $data['is_addon'] ?? 0,
            $data['calculation_method'] ?? 'fixed',
            $data['applies_to'] ?? 'total',
            $data['is_refundable'] ?? 0,
            $data['display_section'] ?? 'production',
            $data['default_price'] ?? null,
            $data['sort_order'] ?? 0,
            $data['metadata'] ?? null,
            $data['is_active'] ?? 1,
            $data['created_by']
        ]);
        
        return $this->db->lastInsertId();
    } catch (PDOException $e) {
        error_log("ProductionProperty creation error: " . $e->getMessage());
        return false;
    }
}

    
    /**
     * Find property by ID
     * 
     * @param int $id Property ID
     * @return array|false
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("ProductionProperty find error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find property by code
     * 
     * @param string $code Property code
     * @return array|false
     */
    public function findByCode($code) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE code = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$code]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("ProductionProperty find by code error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all properties with pagination
     * 
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT pp.*, u.name as created_by_name 
                    FROM {$this->table} pp
                    LEFT JOIN users u ON pp.created_by = u.id
                    WHERE pp.deleted_at IS NULL 
                    ORDER BY pp.category ASC, pp.sort_order ASC, pp.id ASC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$limit, (int)$offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("ProductionProperty fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get active properties by category
     * 
     * @param string $category Category (alusteel, aluminum, kzinc)
     * @return array
     */
    public function getByCategoryAndActive($category) {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE category = ? AND is_active = 1 AND deleted_at IS NULL 
                    ORDER BY sort_order ASC, name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$category]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("ProductionProperty fetch by category error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all active properties for dropdown
     * 
     * @return array
     */
    public function getActive() {
        try {
            $sql = "SELECT id, code, name, category, property_type, default_price, metadata 
                    FROM {$this->table} 
                    WHERE is_active = 1 AND deleted_at IS NULL 
                    ORDER BY category ASC, sort_order ASC, name ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Active properties fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total properties
     * 
     * @return int
     */
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("ProductionProperty count error: " . $e->getMessage());
            return 0;
        }
    }
    
   /**
 * Update existing update method to support new fields
 * REPLACE the existing update() method with this version
 */
public function update($id, $data) {
    try {
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'code', 'name', 'category', 'property_type', 'is_addon',
            'calculation_method', 'applies_to', 'is_refundable', 
            'display_section', 'default_price', 'sort_order', 
            'metadata', 'is_active'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = NOW()";
        $params[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("ProductionProperty update error: " . $e->getMessage());
        return false;
    }
}
    
    /**
     * Soft delete property
     * 
     * @param int $id Property ID
     * @return bool
     */
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("ProductionProperty delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search properties
     * 
     * @param string $query Search query
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function search($query, $limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT pp.*, u.name as created_by_name 
                    FROM {$this->table} pp
                    LEFT JOIN users u ON pp.created_by = u.id
                    WHERE pp.deleted_at IS NULL 
                    AND (pp.code LIKE ? OR pp.name LIKE ? OR pp.category LIKE ?)
                    ORDER BY pp.category ASC, pp.sort_order ASC 
                    LIMIT ? OFFSET ?";
            
            $searchParam = "%$query%";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchParam, $searchParam, $searchParam, (int)$limit, (int)$offset]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("ProductionProperty search error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count search results
     * 
     * @param string $query Search query
     * @return int
     */
    public function countSearch($query) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    AND (code LIKE ? OR name LIKE ? OR category LIKE ?)";
            
            $searchParam = "%$query%";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchParam, $searchParam, $searchParam]);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("ProductionProperty count search error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Check if property is being used in any production records
     * 
     * @param int $id Property ID
     * @return bool
     */
    public function isUsedInProductions($id) {
        try {
            // Check if property code is referenced in production_paper JSON
            $property = $this->findById($id);
            if (!$property) return false;
            
            $sql = "SELECT COUNT(*) as count FROM productions 
                    WHERE production_paper LIKE ? AND deleted_at IS NULL";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['%"' . $property['code'] . '"%']);
            $result = $stmt->fetch();
            
            return ($result['count'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("ProductionProperty usage check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get properties grouped by category
     * 
     * @return array
     */
    public function getGroupedByCategory() {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    ORDER BY category ASC, sort_order ASC, name ASC";
            
            $stmt = $this->db->query($sql);
            $properties = $stmt->fetchAll();
            
            $grouped = [];
            foreach ($properties as $property) {
                $grouped[$property['category']][] = $property;
            }
            
            return $grouped;
        } catch (PDOException $e) {
            error_log("ProductionProperty grouped fetch error: " . $e->getMessage());
            return [];
        }
    }

    /**
 * EXTENDED ProductionProperty Model - Add new methods to existing model
 * File: models/production_property.php
 * 
 * ADD these methods to the existing ProductionProperty class
 */

// ============================================================
// ADD-ON PROPERTY METHODS
// ============================================================

/**
 * Get production properties only (exclude add-ons)
 * 
 * @param string $category Category filter
 * @return array
 */
public function getProductionPropertiesByCategory($category) {
    try {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category = ? 
                AND is_addon = 0 
                AND is_active = 1 
                AND deleted_at IS NULL 
                ORDER BY sort_order ASC, name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Production properties fetch error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get add-on properties only
 * 
 * @param string $category Category filter
 * @param bool $includeRefunds Include refund/adjustment items
 * @return array
 */
public function getAddonPropertiesByCategory($category, $includeRefunds = true) {
    try {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category = ? 
                AND is_addon = 1 
                AND is_active = 1 
                AND deleted_at IS NULL";
        
        if (!$includeRefunds) {
            $sql .= " AND is_refundable = 0";
        }
        
        $sql .= " ORDER BY display_section ASC, sort_order ASC, name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Add-on properties fetch error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get properties grouped by display section
 * 
 * @param string $category Category filter
 * @return array
 */
public function getPropertiesGroupedBySection($category) {
    try {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category = ? 
                AND is_active = 1 
                AND deleted_at IS NULL 
                ORDER BY display_section ASC, sort_order ASC, name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$category]);
        $properties = $stmt->fetchAll();
        
        $grouped = [
            'production' => [],
            'addon' => [],
            'adjustment' => []
        ];
        
        foreach ($properties as $property) {
            $section = $property['display_section'] ?? 'production';
            $grouped[$section][] = $property;
        }
        
        return $grouped;
    } catch (PDOException $e) {
        error_log("Grouped properties fetch error: " . $e->getMessage());
        return ['production' => [], 'addon' => [], 'adjustment' => []];
    }
}

/**
 * Calculate add-on amount based on calculation method
 * 
 * @param array $property Property configuration
 * @param float $baseAmount Base amount to calculate from
 * @param float $customAmount User-specified amount (overrides default)
 * @return float
 */
public function calculateAddonAmount($property, $baseAmount = 0, $customAmount = null) {
    // If custom amount provided, use it directly
    if ($customAmount !== null) {
        return floatval($customAmount);
    }
    
    $defaultPrice = floatval($property['default_price'] ?? 0);
    $calculationMethod = $property['calculation_method'] ?? 'fixed';
    
    switch ($calculationMethod) {
        case 'fixed':
            return $defaultPrice;
            
        case 'percentage':
            return ($baseAmount * $defaultPrice) / 100;
            
        case 'per_unit':
            // For add-ons, per_unit typically means fixed amount
            return $defaultPrice;
            
        default:
            return $defaultPrice;
    }
}

/**
 * Get all properties for workflow (production + add-ons)
 * 
 * @param string $category Category filter
 * @return array
 */
public function getWorkflowProperties($category) {
    try {
        $sql = "SELECT 
                    *,
                    CASE 
                        WHEN is_addon = 0 THEN 'production'
                        WHEN is_refundable = 1 THEN 'adjustment'
                        ELSE 'addon'
                    END as section_type
                FROM {$this->table} 
                WHERE category = ? 
                AND is_active = 1 
                AND deleted_at IS NULL 
                ORDER BY is_addon ASC, display_section ASC, sort_order ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Workflow properties fetch error: " . $e->getMessage());
        return [];
    }
}

/**
 * Check if property is an add-on
 * 
 * @param int $id Property ID
 * @return bool
 */
public function isAddon($id) {
    try {
        $property = $this->findById($id);
        return $property && ($property['is_addon'] == 1);
    } catch (Exception $e) {
        error_log("Add-on check error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get refund/adjustment properties only
 * 
 * @param string $category Category filter
 * @return array
 */
public function getRefundProperties($category) {
    try {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category = ? 
                AND is_addon = 1 
                AND is_refundable = 1 
                AND is_active = 1 
                AND deleted_at IS NULL 
                ORDER BY sort_order ASC, name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Refund properties fetch error: " . $e->getMessage());
        return [];
    }
}


}