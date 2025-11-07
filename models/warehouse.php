<?php
/**
 * Warehouse Model
 * 
 * Handles all warehouse-related database operations
 * File: models/warehouse.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class Warehouse {
    private $db;
    private $table = 'warehouses';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new warehouse
     * 
     * @param array $data Warehouse data
     * @return int|false Warehouse ID or false on failure
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (name, location, contact, is_active) 
                    VALUES (:name, :location, :contact, :is_active)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':location' => $data['location'] ?? null,
                ':contact' => $data['contact'] ?? null,
                ':is_active' => $data['is_active'] ?? 1
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Warehouse creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find warehouse by ID
     * 
     * @param int $id Warehouse ID
     * @return array|false
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Warehouse find error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all warehouses with pagination
     * 
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getAll($limit = 1000, $offset = 0) {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    ORDER BY name ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Warehouse fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get active warehouses
     * 
     * @return array
     */
    public function getActive() {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE is_active = 1 AND deleted_at IS NULL 
                    ORDER BY name ASC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Active warehouse fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update warehouse
     * 
     * @param int $id Warehouse ID
     * @param array $data Warehouse data
     * @return bool
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $params = [':id' => $id];
            
            $allowedFields = ['name', 'location', 'contact', 'is_active'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $fields[] = "updated_at = NOW()";
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Warehouse update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete warehouse
     * 
     * @param int $id Warehouse ID
     * @return bool
     */
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Warehouse delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count total warehouses
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
            error_log("Warehouse count error: " . $e->getMessage());
            return 0;
        }
    }
}