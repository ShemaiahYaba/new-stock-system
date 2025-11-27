<?php
/**
 * ==============================================
 * FILE: models/tile_product.php
 * ==============================================
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class TileProduct {
    private $db;
    private $table = 'tile_products';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function generateCode($designCode, $colorCode, $gauge) {
        return strtoupper($designCode . '-' . $colorCode . '-' . strtoupper($gauge));
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (code, design_id, color_id, gauge, status, created_by, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['code'],
                $data['design_id'],
                $data['color_id'],
                $data['gauge'],
                $data['status'] ?? 'out_of_stock',
                $data['created_by']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Tile product creation error: " . $e->getMessage());
            return false;
        }
    }
    
    public function findById($id) {
        try {
            $sql = "SELECT tp.*, 
                           d.name as design_name, d.code as design_code,
                           c.name as color_name, c.code as color_code, c.hex_code as color_hex
                    FROM {$this->table} tp
                    LEFT JOIN designs d ON tp.design_id = d.id
                    LEFT JOIN colors c ON tp.color_id = c.id
                    WHERE tp.id = ? AND tp.deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Tile product find error: " . $e->getMessage());
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
            error_log("Tile product find error: " . $e->getMessage());
            return false;
        }
    }
    
    public function exists($designId, $colorId, $gauge) {
        try {
            $sql = "SELECT id FROM {$this->table} 
                    WHERE design_id = ? AND color_id = ? AND gauge = ? 
                    AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$designId, $colorId, $gauge]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Tile product exists check error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll($filters = [], $limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT tp.*, 
                           d.name as design_name, d.code as design_code,
                           c.name as color_name, c.code as color_code, c.hex_code as color_hex,
                           COALESCE((SELECT balance FROM tile_stock_ledger 
                                    WHERE tile_product_id = tp.id 
                                    ORDER BY created_at DESC, id DESC LIMIT 1), 0) as current_stock
                    FROM {$this->table} tp
                    LEFT JOIN designs d ON tp.design_id = d.id
                    LEFT JOIN colors c ON tp.color_id = c.id
                    WHERE tp.deleted_at IS NULL";
            
            $params = [];
            
            if (!empty($filters['design_id'])) {
                $sql .= " AND tp.design_id = ?";
                $params[] = $filters['design_id'];
            }
            
            if (!empty($filters['color_id'])) {
                $sql .= " AND tp.color_id = ?";
                $params[] = $filters['color_id'];
            }
            
            if (!empty($filters['gauge'])) {
                $sql .= " AND tp.gauge = ?";
                $params[] = $filters['gauge'];
            }
            
            if (!empty($filters['status'])) {
                $sql .= " AND tp.status = ?";
                $params[] = $filters['status'];
            }
            
            $sql .= " ORDER BY tp.created_at DESC LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Tile product fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    public function count($filters = []) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
            $params = [];
            
            if (!empty($filters['design_id'])) {
                $sql .= " AND design_id = ?";
                $params[] = $filters['design_id'];
            }
            
            if (!empty($filters['color_id'])) {
                $sql .= " AND color_id = ?";
                $params[] = $filters['color_id'];
            }
            
            if (!empty($filters['gauge'])) {
                $sql .= " AND gauge = ?";
                $params[] = $filters['gauge'];
            }
            
            if (!empty($filters['status'])) {
                $sql .= " AND status = ?";
                $params[] = $filters['status'];
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Tile product count error: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getCurrentStock($id) {
        try {
            $sql = "SELECT COALESCE(balance, 0) as stock 
                    FROM tile_stock_ledger 
                    WHERE tile_product_id = ? 
                    ORDER BY created_at DESC, id DESC 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result ? floatval($result['stock']) : 0;
        } catch (PDOException $e) {
            error_log("Tile product stock fetch error: " . $e->getMessage());
            return 0;
        }
    }
    
    public function updateStatus($id, $status) {
        try {
            $sql = "UPDATE {$this->table} 
                    SET status = ?, updated_at = NOW() 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Tile product status update error: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Tile product delete error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAvailable() {
        try {
            $sql = "SELECT tp.*, 
                           d.name as design_name, c.name as color_name,
                           COALESCE((SELECT balance FROM tile_stock_ledger 
                                    WHERE tile_product_id = tp.id 
                                    ORDER BY created_at DESC LIMIT 1), 0) as current_stock
                    FROM {$this->table} tp
                    LEFT JOIN designs d ON tp.design_id = d.id
                    LEFT JOIN colors c ON tp.color_id = c.id
                    WHERE tp.deleted_at IS NULL 
                    AND tp.status = 'available'
                    HAVING current_stock > 0
                    ORDER BY tp.code ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Available tile products fetch error: " . $e->getMessage());
            return [];
        }
    }
}