<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class TileSale {
    private $db;
    private $table = 'tile_sales';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // 1. Create sale record
            $sql = "INSERT INTO {$this->table} 
                    (customer_id, tile_product_id, quantity, unit_price, 
                     total_amount, status, notes, created_by, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['customer_id'],
                $data['tile_product_id'],
                $data['quantity'],
                $data['unit_price'],
                $data['total_amount'],
                $data['status'] ?? 'completed',
                $data['notes'] ?? null,
                $data['created_by']
            ]);
            
            $saleId = $this->db->lastInsertId();
            
            // 2. Deduct stock from ledger
            $ledgerModel = new TileStockLedger();
            $ledgerSuccess = $ledgerModel->recordStockOut(
                $data['tile_product_id'],
                $data['quantity'],
                'sale',
                $saleId,
                "Sale to customer",
                $data['created_by']
            );
            
            if (!$ledgerSuccess) {
                $this->db->rollBack();
                return false;
            }
            
            // 3. Update product status if out of stock
            $currentStock = $ledgerModel->getCurrentBalance($data['tile_product_id']);
            if ($currentStock <= 0) {
                $productModel = new TileProduct();
                $productModel->updateStatus($data['tile_product_id'], 'out_of_stock');
            }
            
            $this->db->commit();
            return $saleId;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Tile sale creation error: " . $e->getMessage());
            return false;
        }
    }
    
    public function findById($id) {
        try {
            $sql = "SELECT ts.*, 
                           c.name as customer_name, c.phone as customer_phone,
                           tp.code as product_code,
                           d.name as design_name, col.name as color_name,
                           u.name as created_by_name
                    FROM {$this->table} ts
                    LEFT JOIN customers c ON ts.customer_id = c.id
                    LEFT JOIN tile_products tp ON ts.tile_product_id = tp.id
                    LEFT JOIN designs d ON tp.design_id = d.id
                    LEFT JOIN colors col ON tp.color_id = col.id
                    LEFT JOIN users u ON ts.created_by = u.id
                    WHERE ts.id = ? AND ts.deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Tile sale find error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT ts.*, 
                           c.name as customer_name,
                           tp.code as product_code
                    FROM {$this->table} ts
                    LEFT JOIN customers c ON ts.customer_id = c.id
                    LEFT JOIN tile_products tp ON ts.tile_product_id = tp.id
                    WHERE ts.deleted_at IS NULL
                    ORDER BY ts.created_at DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$limit, (int)$offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Tile sales fetch error: " . $e->getMessage());
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
            error_log("Tile sales count error: " . $e->getMessage());
            return 0;
        }
    }
}