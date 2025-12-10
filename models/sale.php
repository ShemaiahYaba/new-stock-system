<?php
/**
 * Sale Model - COMPLETELY FIXED
 * 
 * Handles all sale-related database operations
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class Sale {
    private $db;
    private $table = 'sales';
    private $invoiceTable = 'invoices';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new sale
     * 
     * @param array $data Sale data
     * @return int|false Sale ID or false on failure
     */
     public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (customer_id, coil_id, stock_entry_id, sale_type, meters, weight_kg, 
                     price_per_meter, price_per_kg, total_amount, status, created_by, notes, created_at) 
                    VALUES 
                    (:customer_id, :coil_id, :stock_entry_id, :sale_type, :meters, :weight_kg, 
                     :price_per_meter, :price_per_kg, :total_amount, :status, :created_by, :notes, NOW())";

            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                ':customer_id' => $data['customer_id'],
                ':coil_id' => $data['coil_id'],
                ':stock_entry_id' => $data['stock_entry_id'] ?? null,
                ':sale_type' => $data['sale_type'],
                ':meters' => $data['meters'] ?? 0,
                ':weight_kg' => $data['weight_kg'] ?? null,
                ':price_per_meter' => $data['price_per_meter'] ?? 0,
                ':price_per_kg' => $data['price_per_kg'] ?? null,
                ':total_amount' => $data['total_amount'],
                ':status' => $data['status'],
                ':created_by' => $data['created_by'],
                ':notes' => $data['notes'] ?? null,
            ]);

            if ($result) {
                return $this->db->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log('Sale creation error: ' . $e->getMessage());
            error_log('SQL State: ' . $e->getCode());
            error_log('Data: ' . print_r($data, true));
            return false;
        }
    }
    
    /**
     * Find sale by ID
     * 
     * @param int $id Sale ID
     * @return array|false
     */
    public function findById($id) {
        try {
            $sql = "SELECT s.*, 
                           c.name as customer_name, c.phone as customer_phone,
                           co.code as coil_code, co.name as coil_name, co.status as coil_status,
                           u.name as created_by_name
                    FROM {$this->table} s
                    LEFT JOIN customers c ON s.customer_id = c.id
                    LEFT JOIN coils co ON s.coil_id = co.id
                    LEFT JOIN users u ON s.created_by = u.id
                    WHERE s.id = :id AND s.deleted_at IS NULL";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Sale find error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all sales with pagination
     * 
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            // Simplified query without window functions
            $sql = "SELECT s.*, 
                           c.name as customer_name,
                           co.code as coil_code, co.name as coil_name,
                           u.name as created_by_name
                    FROM {$this->table} s
                    LEFT JOIN customers c ON s.customer_id = c.id
                    LEFT JOIN coils co ON s.coil_id = co.id
                    LEFT JOIN users u ON s.created_by = u.id
                    WHERE s.deleted_at IS NULL
                    ORDER BY s.created_at DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$limit, (int)$offset]);
            
            $sales = $stmt->fetchAll();
            
            // Get invoice data separately for each sale
            foreach ($sales as &$sale) {
                $invoiceSql = "SELECT id, status, invoice_number, paid_amount, total 
                              FROM {$this->invoiceTable} 
                              WHERE sale_id = ? 
                              ORDER BY created_at DESC 
                              LIMIT 1";
                $invoiceStmt = $this->db->prepare($invoiceSql);
                $invoiceStmt->execute([$sale['id']]);
                $invoice = $invoiceStmt->fetch();
                
                $sale['has_invoice'] = !empty($invoice);
                $sale['invoice'] = $invoice ?: null;
            }
            
            return $sales;
        } catch (PDOException $e) {
            error_log("Sale fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total sales
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
            error_log("Sale count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get invoice for sale
     * 
     * @param int $saleId
     * @return array|false
     */
    public function getInvoice($saleId) {
        try {
            $sql = "SELECT * FROM {$this->invoiceTable} WHERE sale_id = ? ORDER BY created_at DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$saleId]);
            
            $invoice = $stmt->fetch();
            
            if ($invoice && isset($invoice['invoice_shape'])) {
                $invoice['invoice_shape'] = json_decode($invoice['invoice_shape'], true);
            }
            
            return $invoice;
        } catch (PDOException $e) {
            error_log("Error getting sale invoice: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if sale has an invoice
     * 
     * @param int $saleId
     * @return bool
     */
    public function hasInvoice($saleId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->invoiceTable} WHERE sale_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$saleId]);
            $result = $stmt->fetch();
            
            return ($result['count'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("Error checking sale invoice: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update sale - with stock_entry_id support
     * 
     * @param int $id Sale ID
     * @param array $data Sale data
     * @return bool
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $params = [];
            
            if (isset($data['stock_entry_id'])) {
                $fields[] = "stock_entry_id = ?";
                $params[] = $data['stock_entry_id'];
            }
            
            if (isset($data['status'])) {
                $fields[] = "status = ?";
                $params[] = $data['status'];
            }
            
            if (isset($data['meters'])) {
                $fields[] = "meters = ?";
                $params[] = $data['meters'];
            }
            
            if (isset($data['price_per_meter'])) {
                $fields[] = "price_per_meter = ?";
                $params[] = $data['price_per_meter'];
            }
            
            if (isset($data['total_amount'])) {
                $fields[] = "total_amount = ?";
                $params[] = $data['total_amount'];
            }
            
            if (empty($fields)) {
                error_log("Sale update called with no fields to update for ID: $id");
                return false;
            }
            
            $fields[] = "updated_at = NOW()";
            $params[] = $id; // Add ID as last parameter
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Sale update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update sale status
     * 
     * @param int $id Sale ID
     * @param string $status New status
     * @return bool
     */
    public function updateStatus($id, $status) {
        try {
            $sql = "UPDATE {$this->table} SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Sale status update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete sale
     * 
     * @param int $id Sale ID
     * @return bool
     */
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Sale delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get sales by customer
     * 
     * @param int $customerId Customer ID
     * @return array
     */
    public function getByCustomer($customerId) {
        try {
            $sql = "SELECT s.*, co.code as coil_code, co.name as coil_name
                    FROM {$this->table} s
                    LEFT JOIN coils co ON s.coil_id = co.id
                    WHERE s.customer_id = ? AND s.deleted_at IS NULL
                    ORDER BY s.created_at ASC, s.id ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$customerId]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Sale fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get sales by coil
     * 
     * @param int $coilId Coil ID
     * @return array
     */
    public function getByCoil($coilId) {
        try {
            $sql = "SELECT s.*, c.name as customer_name
                    FROM {$this->table} s
                    LEFT JOIN customers c ON s.customer_id = c.id
                    WHERE s.coil_id = ? AND s.deleted_at IS NULL
                    ORDER BY s.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$coilId]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Sale fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total sales amount
     * 
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return float
     */
    public function getTotalSalesAmount($startDate = null, $endDate = null) {
        try {
            $sql = "SELECT SUM(total_amount) as total 
                    FROM {$this->table} 
                    WHERE status = ? AND deleted_at IS NULL";
            
            $params = [SALE_STATUS_COMPLETED];
            
            if ($startDate) {
                $sql .= " AND created_at >= ?";
                $params[] = $startDate;
            }
            
            if ($endDate) {
                $sql .= " AND created_at <= ?";
                $params[] = $endDate;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Total sales amount fetch error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get filtered sales with pagination - USING POSITIONAL PARAMETERS
     * 
     * @param string $whereClause WHERE clause with conditions
     * @param array $params Query parameters (positional, not named)
     * @param int $limit Records per page
     * @param int $offset Offset for pagination
     * @return array
     */
    public function getFilteredSales($whereClause, $params = [], $limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT s.*, 
                           c.name as customer_name,
                           co.code as coil_code, co.name as coil_name,
                           u.name as created_by_name
                    FROM {$this->table} s
                    LEFT JOIN customers c ON s.customer_id = c.id
                    LEFT JOIN coils co ON s.coil_id = co.id
                    LEFT JOIN users u ON s.created_by = u.id
                    $whereClause
                    ORDER BY s.created_at DESC
                    LIMIT ? OFFSET ?";
            
            // Add pagination parameters to the end
            $allParams = array_values($params);
            $allParams[] = (int)$limit;
            $allParams[] = (int)$offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($allParams);
            
            $sales = $stmt->fetchAll();
            
            // Get invoice data separately for each sale
            foreach ($sales as &$sale) {
                $invoiceSql = "SELECT id, status, invoice_number, paid_amount, total 
                              FROM {$this->invoiceTable} 
                              WHERE sale_id = ? 
                              ORDER BY created_at DESC 
                              LIMIT 1";
                $invoiceStmt = $this->db->prepare($invoiceSql);
                $invoiceStmt->execute([$sale['id']]);
                $invoice = $invoiceStmt->fetch();
                
                $sale['has_invoice'] = !empty($invoice);
                $sale['invoice'] = $invoice ?: null;
            }
            
            return $sales;
            
        } catch (PDOException $e) {
            error_log("Filtered sales fetch error: " . $e->getMessage());
            error_log("SQL: " . ($sql ?? 'N/A'));
            error_log("Params count: " . count($allParams ?? []));
            return [];
        }
    }
    
    /**
     * Count filtered sales - USING POSITIONAL PARAMETERS
     * 
     * @param string $whereClause WHERE clause with conditions
     * @param array $params Query parameters (positional, not named)
     * @return int
     */
    public function countFilteredSales($whereClause, $params = []) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM {$this->table} s
                    LEFT JOIN customers c ON s.customer_id = c.id
                    LEFT JOIN coils co ON s.coil_id = co.id
                    $whereClause";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($params));
            $result = $stmt->fetch();
            
            return (int)($result['total'] ?? 0);
            
        } catch (PDOException $e) {
            error_log("Filtered sales count error: " . $e->getMessage());
            error_log("SQL: " . ($sql ?? 'N/A'));
            return 0;
        }
    }
    
    /**
     * Search sales (kept for backward compatibility)
     * 
     * @deprecated Use getFilteredSales instead
     */
    public function search($query, $limit = RECORDS_PER_PAGE, $offset = 0) {
        $whereClause = 'WHERE s.deleted_at IS NULL 
                       AND (c.name LIKE ? OR co.code LIKE ? OR co.name LIKE ?)';
        
        $searchParam = "%$query%";
        return $this->getFilteredSales(
            $whereClause, 
            [$searchParam, $searchParam, $searchParam], 
            $limit, 
            $offset
        );
    }
}