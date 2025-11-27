<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class TileStockLedger {
    private $db;
    private $table = 'tile_stock_ledger';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function recordStockIn($productId, $quantity, $code, $description, $userId) {
        try {
            $currentBalance = $this->getCurrentBalance($productId);
            $newBalance = $currentBalance + $quantity;
            
            $sql = "INSERT INTO {$this->table} 
                    (tile_product_id, transaction_code, quantity_in, quantity_out, 
                     balance, reference_type, description, created_by, created_at) 
                    VALUES (?, ?, ?, 0, ?, 'stock_in', ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$productId, $code, $quantity, $newBalance, $description, $userId]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Tile stock IN error: " . $e->getMessage());
            return false;
        }
    }
    
    public function recordStockOut($productId, $quantity, $referenceType, $referenceId, $description, $userId) {
        try {
            $currentBalance = $this->getCurrentBalance($productId);
            
            if ($currentBalance < $quantity) {
                error_log("Insufficient tile stock for product $productId");
                return false;
            }
            
            $newBalance = $currentBalance - $quantity;
            
            $sql = "INSERT INTO {$this->table} 
                    (tile_product_id, quantity_in, quantity_out, balance, 
                     reference_type, reference_id, description, created_by, created_at) 
                    VALUES (?, 0, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$productId, $quantity, $newBalance, $referenceType, $referenceId, $description, $userId]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Tile stock OUT error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getCurrentBalance($productId) {
        try {
            $sql = "SELECT balance FROM {$this->table} 
                    WHERE tile_product_id = ? 
                    ORDER BY created_at DESC, id DESC 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$productId]);
            $result = $stmt->fetch();
            return $result ? floatval($result['balance']) : 0;
        } catch (PDOException $e) {
            error_log("Tile balance fetch error: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getByProduct($productId, $limit = 100, $offset = 0) {
        try {
            $sql = "SELECT tsl.*, u.name as created_by_name,
                           c.name as customer_name
                    FROM {$this->table} tsl
                    LEFT JOIN users u ON tsl.created_by = u.id
                    LEFT JOIN tile_sales ts ON tsl.reference_type = 'sale' AND tsl.reference_id = ts.id
                    LEFT JOIN customers c ON ts.customer_id = c.id
                    WHERE tsl.tile_product_id = ?
                    ORDER BY tsl.created_at DESC, tsl.id DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$productId, (int)$limit, (int)$offset]);
            
            // Process results to update descriptions for sales
            $results = $stmt->fetchAll();
            foreach ($results as &$row) {
                if ($row['reference_type'] === 'sale' && !empty($row['customer_name'])) {
                    $row['description'] = 'Sale to ' . $row['customer_name'];
                }
            }
            return $results;
        } catch (PDOException $e) {
            error_log("Tile ledger fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    public function canDeductStock($productId, $quantity) {
        return $this->getCurrentBalance($productId) >= $quantity;
    }
}