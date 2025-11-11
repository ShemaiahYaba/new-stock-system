<?php
/**
 * Color Model
 * 
 * Handles all color-related database operations
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class Color {
    private $db;
    private $table = 'colors';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new color
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (code, name, hex_code, is_active, created_by, created_at) 
                    VALUES (:code, :name, :hex_code, :is_active, :created_by, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':code' => $data['code'],
                ':name' => $data['name'],
                ':hex_code' => $data['hex_code'] ?? null,
                ':is_active' => $data['is_active'] ?? 1,
                ':created_by' => $data['created_by']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Color creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find color by ID
     */
    /**
     * Find color by ID
     * @param int $id Color ID to find
     * @return array|false Returns color data as array or false if not found/error
     */
    public function findById($id): array|false {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Color find error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find color by code
     */
    public function findByCode($code) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE code = :code AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':code' => $code]);
            return $stmt->fetch();
        }catch (PDOException $e) {
            error_log("Color find error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all colors with pagination
     */
    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT c.*, u.name as created_by_name 
                    FROM {$this->table} c
                    LEFT JOIN users u ON c.created_by = u.id
                    WHERE c.deleted_at IS NULL 
                    ORDER BY c.name ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Color fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get active colors for dropdown
     */
    public function getActive() {
        try {
            $sql = "SELECT id, code, name, hex_code 
                    FROM {$this->table} 
                    WHERE is_active = 1 AND deleted_at IS NULL 
                    ORDER BY name ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Active colors fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total colors
     */
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Color count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Update color
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $params = [':id' => $id];
            
            $allowedFields = ['code', 'name', 'hex_code', 'is_active'];
            
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
            error_log("Color update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete color
     */
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Color delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search colors
     */
    public function search($query, $limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT c.*, u.name as created_by_name 
                    FROM {$this->table} c
                    LEFT JOIN users u ON c.created_by = u.id
                    WHERE c.deleted_at IS NULL 
                    AND (c.code LIKE :query OR c.name LIKE :query)
                    ORDER BY c.name ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Color search error: " . $e->getMessage());
            return [];
        }
    }
}