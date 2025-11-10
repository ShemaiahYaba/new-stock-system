<?php
/**
 * Stock Ledger Model
 *
 * Tracks all stock movements with dual-entry accounting
 * Maintains independent running balance for each stock entry
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class StockLedger
{
    private $db;
    private $table = 'stock_ledger';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a ledger entry
     *
     * @param array $data Ledger data
     * @return int|false Ledger ID or false on failure
     */
    public function create($data)
    {
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
                ':created_by' => $data['created_by'],
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Stock ledger creation error: ' . $e->getMessage());
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
    public function getByCoil($coilId, $limit = 100, $offset = 0)
    {
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
            error_log('Stock ledger fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * FIXED: Get ledger entries for a specific stock entry
     *
     * @param int $stockEntryId Stock Entry ID
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getByStockEntry($stockEntryId, $limit = 100, $offset = 0)
    {
        try {
            $sql = "SELECT sl.*, u.name as created_by_name,
                           se.meters as entry_total_meters,
                           c.code as coil_code,
                           c.name as coil_name
                    FROM {$this->table} sl
                    LEFT JOIN users u ON sl.created_by = u.id
                    LEFT JOIN stock_entries se ON sl.stock_entry_id = se.id
                    LEFT JOIN coils c ON sl.coil_id = c.id
                    WHERE sl.stock_entry_id = :stock_entry_id
                    ORDER BY sl.created_at DESC, sl.id DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':stock_entry_id', $stockEntryId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Stock ledger fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get ledger summary for a coil
     *
     * @param int $coilId Coil ID
     * @return array
     */
    public function getSummary($coilId)
    {
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
            error_log('Stock ledger summary error: ' . $e->getMessage());
            return [
                'total_inflow' => 0,
                'total_outflow' => 0,
                'current_balance' => 0,
            ];
        }
    }

    /**
     * FIXED: Get current balance for a stock entry
     * Each stock entry maintains its own independent balance
     *
     * @param int $stockEntryId Stock Entry ID
     * @return float
     */
    public function getCurrentBalance($stockEntryId)
    {
        try {
            $sql = "SELECT balance_meters 
                    FROM {$this->table} 
                    WHERE stock_entry_id = :stock_entry_id 
                    ORDER BY created_at DESC, id DESC 
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':stock_entry_id' => $stockEntryId]);
            $result = $stmt->fetch();

            return $result ? floatval($result['balance_meters']) : 0;
        } catch (PDOException $e) {
            error_log('Stock ledger balance error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * FIXED: Get total balance across all stock entries for a coil
     * Useful for getting the cumulative available meters across all entries
     *
     * @param int $coilId Coil ID
     * @return float
     */
    public function getTotalBalanceForCoil($coilId)
    {
        try {
            // Get the latest balance for each stock entry of this coil
            $sql = "SELECT SUM(latest.balance_meters) as total
                    FROM (
                        SELECT DISTINCT ON (stock_entry_id) 
                               stock_entry_id, 
                               balance_meters
                        FROM {$this->table}
                        WHERE coil_id = :coil_id 
                        AND stock_entry_id IS NOT NULL
                        ORDER BY stock_entry_id, created_at DESC, id DESC
                    ) latest";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            $result = $stmt->fetch();

            return $result ? floatval($result['total']) : 0;
        } catch (PDOException $e) {
            error_log('Stock ledger total balance error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * FIXED: Record stock inflow (addition)
     * Balance is now calculated per stock_entry_id
     *
     * @param int $coilId Coil ID
     * @param int $stockEntryId Stock Entry ID
     * @param float $meters Meters to add
     * @param string $description Description
     * @param int $createdBy User ID
     * @return int|false
     */
    public function recordInflow($coilId, $stockEntryId, $meters, $description, $createdBy)
    {
        // Get balance for THIS specific stock entry
        $currentBalance = $this->getCurrentBalance($stockEntryId);
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
            'created_by' => $createdBy,
        ]);
    }

    /**
     * FIXED: Record stock outflow (removal/sale)
     * Balance is now calculated per stock_entry_id
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
    public function recordOutflow(
        $coilId,
        $stockEntryId,
        $meters,
        $description,
        $referenceType,
        $referenceId,
        $createdBy,
    ) {
        // Get balance for THIS specific stock entry
        $currentBalance = $this->getCurrentBalance($stockEntryId);
        $newBalance = $currentBalance - $meters;

        // Get the stock entry to validate available meters
        $stockEntryModel = new StockEntry();
        $stockEntry = $stockEntryModel->findById($stockEntryId);

        // Prevent outflow if not enough meters available in the stock entry
        if ($stockEntry && $meters > $stockEntry['meters_remaining']) {
            error_log(
                "Stock ledger: Attempted to record outflow of {$meters}m but only {$stockEntry['meters_remaining']}m available for entry $stockEntryId",
            );
            return false;
        }

        // Check for negative balance in THIS stock entry
        if ($newBalance < 0) {
            error_log(
                "Stock ledger: Attempted negative balance for stock entry $stockEntryId (current: $currentBalance, outflow: $meters)",
            );
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
            'created_by' => $createdBy,
        ]);
    }

    /**
     * UPDATED: Get all factory-use stock entries with ledger data
     * Now shows each stock entry independently
     *
     * @return array
     */
    public function getFactoryUseStockEntries()
    {
        try {
            $sql = "SELECT 
                        se.id as stock_entry_id,
                        se.coil_id,
                        se.meters as total_meters,
                        se.meters_remaining,
                        se.created_at,
                        c.code as coil_code,
                        c.name as coil_name,
                        c.color as coil_color,
                        c.category as coil_category,
                        (SELECT SUM(inflow_meters) FROM {$this->table} WHERE stock_entry_id = se.id) as total_inflow,
                        (SELECT SUM(outflow_meters) FROM {$this->table} WHERE stock_entry_id = se.id) as total_outflow,
                        (SELECT balance_meters FROM {$this->table} WHERE stock_entry_id = se.id ORDER BY created_at DESC, id DESC LIMIT 1) as current_balance
                    FROM stock_entries se
                    INNER JOIN coils c ON se.coil_id = c.id
                    WHERE se.status = :status 
                    AND se.deleted_at IS NULL
                    AND c.deleted_at IS NULL
                    ORDER BY se.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':status' => STOCK_STATUS_FACTORY_USE]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Factory use stock entries fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * DEPRECATED: Use getFactoryUseStockEntries() instead
     * Kept for backward compatibility
     *
     * @return array
     */
    public function getFactoryUseCoils()
    {
        return $this->getFactoryUseStockEntries();
    }
}
