<?php
/**
 * Sale Model
 * 
 * Handles all sale-related database operations
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class Sale {
    private $db;
    private $table = 'sales';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new sale
     * 
     * @param array $data Sale data
     * @return int|false Sale ID or false on failure
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (customer_id, coil_id, stock_entry_id, sale_type, meters, price_per_meter, 
                     total_amount, status, created_by, created_at) 
                    VALUES (:customer_id, :coil_id, :stock_entry_id, :sale_type, :meters, 
                            :price_per_meter, :total_amount, :status, :created_by, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':customer_id' => $data['customer_id'],
                ':coil_id' => $data['coil_id'],
                ':stock_entry_id' => $data['stock_entry_id'] ?? null,
                ':sale_type' => $data['sale_type'],
                ':meters' => $data['meters'],
                ':price_per_meter' => $data['price_per_meter'],
                ':total_amount' => $data['total_amount'],
                ':status' => $data['status'] ?? SALE_STATUS_PENDING,
                ':created_by' => $data['created_by']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Sale creation error: " . $e->getMessage());
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
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
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
     * Update sale
     * 
     * @param int $id Sale ID
     * @param array $data Sale data
     * @return bool
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $params = [':id' => $id];
            
            if (isset($data['status'])) {
                $fields[] = "status = :status";
                $params[':status'] = $data['status'];
            }
            
            if (isset($data['meters'])) {
                $fields[] = "meters = :meters";
                $params[':meters'] = $data['meters'];
            }
            
            if (isset($data['price_per_meter'])) {
                $fields[] = "price_per_meter = :price_per_meter";
                $params[':price_per_meter'] = $data['price_per_meter'];
            }
            
            if (isset($data['total_amount'])) {
                $fields[] = "total_amount = :total_amount";
                $params[':total_amount'] = $data['total_amount'];
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $fields[] = "updated_at = NOW()";
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
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
            $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':status' => $status
            ]);
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
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([':id' => $id]);
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
                    WHERE s.customer_id = :customer_id AND s.deleted_at IS NULL
                    ORDER BY s.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':customer_id' => $customerId]);
            
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
                    WHERE s.coil_id = :coil_id AND s.deleted_at IS NULL
                    ORDER BY s.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            
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
                    WHERE status = :status AND deleted_at IS NULL";
            
            $params = [':status' => SALE_STATUS_COMPLETED];
            
            if ($startDate) {
                $sql .= " AND created_at >= :start_date";
                $params[':start_date'] = $startDate;
            }
            
            if ($endDate) {
                $sql .= " AND created_at <= :end_date";
                $params[':end_date'] = $endDate;
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
     * Get filtered sales with pagination
     * 
     * @param string $whereClause WHERE clause with conditions
     * @param array $params Query parameters
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
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind search parameters
            foreach ($params as $key => $value) {
                $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $paramType);
            }
            
            // Bind pagination parameters
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Filtered sales fetch error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Count filtered sales
     * 
     * @param string $whereClause WHERE clause with conditions
     * @param array $params Query parameters
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
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $paramType);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            return (int)($result['total'] ?? 0);
            
        } catch (PDOException $e) {
            error_log("Filtered sales count error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Search sales (kept for backward compatibility)
     * 
     * @deprecated Use getFilteredSales instead
     */
    public function search($query, $limit = RECORDS_PER_PAGE, $offset = 0) {
        $whereClause = 'WHERE s.deleted_at IS NULL 
                       AND (c.name LIKE :query OR co.code LIKE :query OR co.name LIKE :query)';
        
        return $this->getFilteredSales(
            $whereClause, 
            [':query' => "%$query%"], 
            $limit, 
            $offset
        );
    }
}
