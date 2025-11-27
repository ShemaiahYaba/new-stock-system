<?php
/**
 * ==============================================
 * FILE: models/design.php
 * ==============================================
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class Design {
    private $db;
    private $table = 'designs';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (code, name, description, is_active, created_by, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['code'],
                $data['name'],
                $data['description'] ?? null,
                $data['is_active'] ?? 1,
                $data['created_by']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Design creation error: " . $e->getMessage());
            return false;
        }
    }
    
    public function findById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Design find error: " . $e->getMessage());
            return false;
        }
    }
    
    public function findByCode($code) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE code = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$code]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Design find error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT d.*, u.name as created_by_name 
                    FROM {$this->table} d
                    LEFT JOIN users u ON d.created_by = u.id
                    WHERE d.deleted_at IS NULL 
                    ORDER BY d.name ASC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$limit, (int)$offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Design fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getActive() {
        try {
            $sql = "SELECT id, code, name 
                    FROM {$this->table} 
                    WHERE is_active = 1 AND deleted_at IS NULL 
                    ORDER BY name ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Active designs fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Design count error: " . $e->getMessage());
            return 0;
        }
    }
    
    public function update($id, $data) {
        try {
            $fields = [];
            $params = [];
            
            $allowedFields = ['code', 'name', 'description', 'is_active'];
            
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
            error_log("Design update error: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Design delete error: " . $e->getMessage());
            return false;
        }
    }
    
    public function isUsedInProducts($id) {
        try {
            $sql = "SELECT COUNT(*) as count FROM tile_products 
                    WHERE design_id = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return ($result['count'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("Design usage check error: " . $e->getMessage());
            return false;
        }
    }
    
    public function search($query, $limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $searchParam = "%$query%";
            $sql = "SELECT d.*, u.name as created_by_name 
                    FROM {$this->table} d
                    LEFT JOIN users u ON d.created_by = u.id
                    WHERE d.deleted_at IS NULL 
                    AND (d.code LIKE ? OR d.name LIKE ?)
                    ORDER BY d.name ASC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchParam, $searchParam, (int)$limit, (int)$offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Design search error: " . $e->getMessage());
            return [];
        }
    }
}