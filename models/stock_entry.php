<?php
/**
 * Stock Entry Model
 * 
 * Handles stock entry operations (meter specifications for coils)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class StockEntry {
    private $db;
    private $table = 'stock_entries';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new stock entry
     * 
     * @param array $data Stock entry data
     * @return int|false Stock entry ID or false on failure
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (coil_id, meters, meters_remaining, created_by, created_at) 
                    VALUES (:coil_id, :meters, :meters_remaining, :created_by, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':coil_id' => $data['coil_id'],
                ':meters' => $data['meters'],
                ':meters_remaining' => $data['meters'],
                ':created_by' => $data['created_by']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Stock entry creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find stock entry by ID
     * 
     * @param int $id Stock entry ID
     * @return array|false
     */
    public function findById($id) {
        try {
            $sql = "SELECT se.*, c.code as coil_code, c.name as coil_name, c.status as coil_status
                    FROM {$this->table} se
                    LEFT JOIN coils c ON se.coil_id = c.id
                    WHERE se.id = :id AND se.deleted_at IS NULL";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Stock entry find error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get stock entries by coil ID
     * 
     * @param int $coilId Coil ID
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getByCoilId($coilId, $limit = 1000, $offset = 0) {
        try {
            $sql = "SELECT se.*, u.name as created_by_name
                    FROM {$this->table} se
                    LEFT JOIN users u ON se.created_by = u.id
                    WHERE se.coil_id = :coil_id AND se.deleted_at IS NULL
                    ORDER BY se.created_at DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':coil_id', $coilId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Stock entry fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Alias for getByCoilId for backward compatibility
     * 
     * @param int $coilId Coil ID
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getByCoil($coilId, $limit = 1000, $offset = 0) {
        return $this->getByCoilId($coilId, $limit, $offset);
    }
    
    /**
     * Get all stock entries with pagination
     * 
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT se.*, c.code as coil_code, c.name as coil_name, c.status as coil_status, u.name as created_by_name
                    FROM {$this->table} se
                    LEFT JOIN coils c ON se.coil_id = c.id
                    LEFT JOIN users u ON se.created_by = u.id
                    WHERE se.deleted_at IS NULL
                    ORDER BY se.created_at DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Stock entry fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total stock entries
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
            error_log("Stock entry count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Update stock entry
     * 
     * @param int $id Stock entry ID
     * @param array $data Stock entry data
     * @return bool
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $params = [':id' => $id];
            
            if (isset($data['meters'])) {
                $fields[] = "meters = :meters";
                $params[':meters'] = $data['meters'];
            }
            
            if (isset($data['meters_remaining'])) {
                $fields[] = "meters_remaining = :meters_remaining";
                $params[':meters_remaining'] = $data['meters_remaining'];
            }
            
            if (isset($data['status'])) {
                $fields[] = "status = :status";
                $params[':status'] = $data['status'];
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $sql = "UPDATE {$this->table} 
                    SET " . implode(', ', $fields) . ", 
                        updated_at = NOW() 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Stock entry update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update remaining meters
     * 
     * @param int $id Stock entry ID
     * @param float $metersUsed Meters used
     * @return bool
     */
    public function updateRemainingMeters($id, $metersUsed) {
        try {
            $sql = "UPDATE {$this->table} 
                    SET meters_remaining = meters_remaining - :meters_used,
                        updated_at = NOW() 
                    WHERE id = :id AND meters_remaining >= :meters_used";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':meters_used' => $metersUsed
            ]);
        } catch (PDOException $e) {
            error_log("Stock entry update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete stock entry
     * 
     * @param int $id Stock entry ID
     * @return bool
     */
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Stock entry delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get available meters for a coil
     * 
     * @param int $coilId Coil ID
     * @return float
     */
    public function getAvailableMeters($coilId) {
        try {
            $sql = "SELECT SUM(meters_remaining) as total 
                    FROM {$this->table} 
                    WHERE coil_id = :coil_id AND deleted_at IS NULL";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Available meters fetch error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get stock entries by status with available meters
     * 
     * @param string $status Status (available or factory_use)
     * @return array
     */
    public function getByStatus($status) {
        try {
            $sql = "SELECT se.*, c.code as coil_code, c.name as coil_name, c.color as coil_color, 
                           c.net_weight as coil_weight, c.category as coil_category
                    FROM {$this->table} se
                    LEFT JOIN coils c ON se.coil_id = c.id
                    WHERE se.status = :status 
                    AND se.meters_remaining > 0 
                    AND se.deleted_at IS NULL
                    ORDER BY se.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':status' => $status]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Stock entry by status fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total meters for a coil
     * 
     * @param int $coilId Coil ID
     * @return float
     */
    public function getTotalMeters($coilId) {
        try {
            $sql = "SELECT SUM(meters) as total 
                    FROM {$this->table} 
                    WHERE coil_id = :coil_id AND deleted_at IS NULL";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Total meters fetch error: " . $e->getMessage());
            return 0;
        }
    }
}
