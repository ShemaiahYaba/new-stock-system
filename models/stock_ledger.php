<?php
/**
 * Stock Ledger Model
 * 
 * Tracks all stock movements with dual-entry accounting
 * Maintains running balance for factory-use coils
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class StockLedger {
    private $db;
    private $table = 'stock_ledger';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a ledger entry
     * 
     * @param array $data Ledger data
     * @return int|false Ledger ID or false on failure
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (coil_id, stock_entry_id, transaction_type, description, 
                     inflow_meters, outflow_meters, balance_meters, 
                     reference_type, reference_id, created_by, created_at) 
                    VALUES (:coil_id, :stock_entry_id, :transaction_type, :description, 
                            :inflow_meters, :outflow_meters, :balance_meters,
                            :reference_type, :reference_id, :created_by, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':coil_id' => $data['coil_id'],
                ':stock_entry_id' => $data['stock_entry_id'] ?? null,
                ':transaction_type' => $data['transaction_type'],
                ':description' => $data['description'],
                ':inflow_meters' => $data['inflow_meters'] ?? 0,
                ':outflow_meters' => $data['outflow_meters'] ?? 0,
                ':balance_meters' => $data['balance_meters'],
                ':reference_type' => $data['reference_type'] ?? null,
                ':reference_id' => $data['reference_id'] ?? null,
                ':created_by' => $data['created_by']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Stock ledger creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get ledger entries for a coil
     * 
     * @param int $coilId Coil ID
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getByCoil($coilId, $limit = 100, $offset = 0) {
        try {
            $sql = "SELECT sl.*, u.name as created_by_name,
                           se.meters as entry_total_meters
                    FROM {$this->table} sl
                    LEFT JOIN users u ON sl.created_by = u.id
                    LEFT JOIN stock_entries se ON sl.stock_entry_id = se.id
                    WHERE sl.coil_id = :coil_id
                    ORDER BY sl.created_at DESC, sl.id DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':coil_id', $coilId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Stock ledger fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get ledger summary for a coil
     * 
     * @param int $coilId Coil ID
     * @return array
     */
    public function getSummary($coilId) {
        try {
            $sql = "SELECT 
                        SUM(inflow_meters) as total_inflow,
                        SUM(outflow_meters) as total_outflow,
                        (SELECT balance_meters FROM {$this->table} 
                         WHERE coil_id = :coil_id 
                         ORDER BY created_at DESC, id DESC 
                         LIMIT 1) as current_balance
                    FROM {$this->table}
                    WHERE coil_id = :coil_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Stock ledger summary error: " . $e->getMessage());
            return [
                'total_inflow' => 0,
                'total_outflow' => 0,
                'current_balance' => 0
            ];
        }
    }
    
    /**
     * Get current balance for a coil
     * 
     * @param int $coilId Coil ID
     * @return float
     */
    public function getCurrentBalance($coilId) {
        try {
            $sql = "SELECT balance_meters 
                    FROM {$this->table} 
                    WHERE coil_id = :coil_id 
                    ORDER BY created_at DESC, id DESC 
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            $result = $stmt->fetch();
            
            return $result ? floatval($result['balance_meters']) : 0;
        } catch (PDOException $e) {
            error_log("Stock ledger balance error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Record stock inflow (addition)
     * 
     * @param int $coilId Coil ID
     * @param int $stockEntryId Stock Entry ID
     * @param float $meters Meters to add
     * @param string $description Description
     * @param int $createdBy User ID
     * @return int|false
     */
    public function recordInflow($coilId, $stockEntryId, $meters, $description, $createdBy) {
        $currentBalance = $this->getCurrentBalance($coilId);
        $newBalance = $currentBalance + $meters;
        
        return $this->create([
            'coil_id' => $coilId,
            'stock_entry_id' => $stockEntryId,
            'transaction_type' => 'inflow',
            'description' => $description,
            'inflow_meters' => $meters,
            'outflow_meters' => 0,
            'balance_meters' => $newBalance,
            'reference_type' => 'stock_entry',
            'reference_id' => $stockEntryId,
            'created_by' => $createdBy
        ]);
    }
    
    /**
     * Record stock outflow (removal/sale)
     * 
     * @param int $coilId Coil ID
     * @param int $stockEntryId Stock Entry ID
     * @param float $meters Meters to remove
     * @param string $description Description
     * @param string $referenceType Reference type (e.g., 'sale', 'wastage')
     * @param int $referenceId Reference ID
     * @param int $createdBy User ID
     * @return int|false
     */
    public function recordOutflow($coilId, $stockEntryId, $meters, $description, $referenceType, $referenceId, $createdBy) {
        $currentBalance = $this->getCurrentBalance($coilId);
        $newBalance = $currentBalance - $meters;
        
        // Prevent negative balance
        if ($newBalance < 0) {
            error_log("Stock ledger: Attempted negative balance for coil $coilId");
            return false;
        }
        
        return $this->create([
            'coil_id' => $coilId,
            'stock_entry_id' => $stockEntryId,
            'transaction_type' => 'outflow',
            'description' => $description,
            'inflow_meters' => 0,
            'outflow_meters' => $meters,
            'balance_meters' => $newBalance,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_by' => $createdBy
        ]);
    }
    
    /**
     * Get all factory-use coils with ledger data
     * 
     * @return array
     */
    public function getFactoryUseCoils() {
        try {
            $sql = "SELECT c.*, 
                           (SELECT SUM(inflow_meters) FROM {$this->table} WHERE coil_id = c.id) as total_inflow,
                           (SELECT SUM(outflow_meters) FROM {$this->table} WHERE coil_id = c.id) as total_outflow,
                           (SELECT balance_meters FROM {$this->table} WHERE coil_id = c.id ORDER BY created_at DESC, id DESC LIMIT 1) as current_balance
                    FROM coils c
                    WHERE c.status = :status AND c.deleted_at IS NULL
                    ORDER BY c.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':status' => STOCK_STATUS_FACTORY_USE]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Factory use coils fetch error: " . $e->getMessage());
            return [];
        }
    }
}
