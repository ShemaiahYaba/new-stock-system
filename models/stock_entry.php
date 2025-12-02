<?php
/**
 * Stock Entry Model
 *
 * Handles stock entry operations (meter specifications for coils)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class StockEntry
{
    private $db;
    private $table = 'stock_entries';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new stock entry
     *
     * @param array $data Stock entry data
     * @return int|false Stock entry ID or false on failure
     */
    public function create($data)
{
    try {
        $sql = "INSERT INTO {$this->table} 
                (coil_id, meters, meters_remaining, weight_kg, weight_kg_remaining, created_by, created_at) 
                VALUES (:coil_id, :meters, :meters_remaining, :weight_kg, :weight_kg_remaining, :created_by, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':coil_id' => $data['coil_id'],
            ':meters' => $data['meters'],
            ':meters_remaining' => $data['meters'],
            ':weight_kg' => $data['weight_kg'] ?? null,
            ':weight_kg_remaining' => $data['weight_kg_remaining'] ?? null,
            ':created_by' => $data['created_by'],
        ]);

        return $this->db->lastInsertId();
    } catch (PDOException $e) {
        error_log('Stock entry creation error: ' . $e->getMessage());
        return false;
    }
}

    /**
     * Find stock entry by ID
     *
     * @param int $id Stock entry ID
     * @return array|false
     */
    public function findById($id)
    {
        try {
            $sql = "SELECT se.*, c.code as coil_code, c.name as coil_name, c.status as coil_status
                    FROM {$this->table} se
                    LEFT JOIN coils c ON se.coil_id = c.id
                    WHERE se.id = :id AND se.deleted_at IS NULL";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Stock entry find error: ' . $e->getMessage());
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
    public function getByCoilId($coilId, $limit = 1000, $offset = 0)
    {
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
            error_log('Stock entry fetch error: ' . $e->getMessage());
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
    public function getByCoil($coilId, $limit = 1000, $offset = 0, $onlyAvailable = true)
    {
        $entries = $this->getByCoilId($coilId, $limit, $offset);

        if ($onlyAvailable) {
            return array_filter($entries, function ($entry) {
                return $entry['meters_remaining'] > 0 && $entry['deleted_at'] === null;
            });
        }

        return $entries;
    }

    /**
     * Get all stock entries with pagination
     *
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0)
    {
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
            error_log('Stock entry fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total stock entries
     *
     * @return int
     */
    public function count()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();

            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log('Stock entry count error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update stock entry
     *
     * @param int $id Stock entry ID
     * @param array $data Update data
     * @return bool
     */
    public function update($id, $data)
    {
        try {
            $fields = [];
            $params = [':id' => $id];

            $allowedFields = ['meters_remaining', 'status'];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }

            if (empty($fields)) {
                return false;
            }

            $fields[] = 'updated_at = NOW()';

            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . ' WHERE id = :id';
            $stmt = $this->db->prepare($sql);

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Stock entry update error: ' . $e->getMessage());
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
    public function updateRemainingMeters($id, $metersUsed)
    {
        try {
            $sql = "UPDATE {$this->table} 
                    SET meters_remaining = meters_remaining - :meters_used,
                        updated_at = NOW() 
                    WHERE id = :id AND meters_remaining >= :meters_used";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':id' => $id,
                ':meters_used' => $metersUsed,
            ]);
        } catch (PDOException $e) {
            error_log('Stock entry update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft delete stock entry
     *
     * @param int $id Stock entry ID
     * @return bool
     */
    public function delete($id)
    {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Stock entry delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available meters for a coil
     *
     * @param int $coilId Coil ID
     * @return float
     */
    public function getAvailableMeters($coilId)
    {
        try {
            $sql = "SELECT SUM(meters_remaining) as total 
                    FROM {$this->table} 
                    WHERE coil_id = :coil_id AND deleted_at IS NULL";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            $result = $stmt->fetch();

            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log('Available meters fetch error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all available stock entries
     *
     * @return array Array of available stock entries
     */
 public function getAvailableStock()
{
    try {
        $sql = "SELECT se.*, 
                       c.code as coil_code, 
                       c.name as coil_name, 
                       COALESCE(se.weight_kg, 0) as weight_kg, 
                       COALESCE(se.weight_kg_remaining, 
                               (se.weight_kg * (se.meters_remaining / NULLIF(se.meters, 0))), 
                               0) as weight_kg_remaining
                FROM {$this->table} se
                JOIN coils c ON se.coil_id = c.id
                WHERE se.status = :status 
                AND se.meters_remaining > 0
                AND se.deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => STOCK_STATUS_AVAILABLE]);

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error fetching available stock: ' . $e->getMessage());
        return [];
    }
}

    /**
     * Update stock entry status and usage
     *
     * @param int $id Stock entry ID
     * @param string $status New status
     * @param float $metersUsed Meters used
     * @return bool True on success, false on failure
     */
    public function updateStatusAndUsage($id, $status, $metersUsed)
    {
        try {
            // First, verify the stock entry exists
            $current = $this->findById($id);
            if (!$current) {
                throw new Exception('Stock entry not found: ' . $id);
            }

            // Log current state
            error_log('Updating stock entry ID: ' . $id);
            error_log('Current status: ' . $current['status']);
            error_log('Current meters_remaining: ' . $current['meters_remaining']);
            error_log('Requested meters_used: ' . $metersUsed);

            // Validate meters_remaining
            if ($current['meters_remaining'] < $metersUsed) {
                throw new Exception(
                    'Insufficient meters remaining. Available: ' .
                        $current['meters_remaining'] .
                        ', Requested: ' .
                        $metersUsed,
                );
            }

            // Validate status - only allow changing between available and sold
            $validStatuses = [STOCK_STATUS_AVAILABLE, STOCK_STATUS_SOLD];
            if (!in_array($status, $validStatuses)) {
                throw new Exception(
                    'Invalid status: ' .
                        $status .
                        '. Must be one of: ' .
                        implode(', ', $validStatuses),
                );
            }

            // First, check if meters_used column exists
            $checkColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'meters_used'");
            if ($checkColumn->rowCount() === 0) {
                // Column doesn't exist, add it
                $this->db->exec(
                    "ALTER TABLE {$this->table} ADD COLUMN meters_used DECIMAL(10,2) DEFAULT 0.00 AFTER meters_remaining",
                );
            }

            // Build the update query
            $sql = "UPDATE {$this->table} 
                    SET status = :status,
                        meters_used = COALESCE(meters_used, 0) + :meters_used,
                        meters_remaining = meters_remaining - :meters_to_deduct,
                        updated_at = NOW()
                    WHERE id = :id";

            error_log('Executing SQL: ' . $sql);
            error_log(
                "With params: id=$id, status=$status, meters_used=$metersUsed, meters_to_deduct=$metersUsed",
            );

            $stmt = $this->db->prepare($sql);
            $params = [
                ':id' => $id,
                ':status' => $status,
                ':meters_used' => (float) $metersUsed,
                ':meters_to_deduct' => (float) $metersUsed,
            ];

            $result = $stmt->execute($params);
            $rowCount = $stmt->rowCount();

            error_log('Update result: ' . ($result ? 'true' : 'false'));
            error_log('Rows affected: ' . $rowCount);

            if (!$result) {
                $errorInfo = $this->db->errorInfo();
                throw new Exception('Database error: ' . ($errorInfo[2] ?? 'Unknown error'));
            }

            if ($rowCount === 0) {
                throw new Exception(
                    'No rows were updated. Check if the record exists and the values are different.',
                );
            }

            return true;
        } catch (Exception $e) {
            error_log('Error in updateStatusAndUsage: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            if (isset($this->db)) {
                $errorInfo = $this->db->errorInfo();
                error_log('Database error info: ' . print_r($errorInfo, true));
            }
            throw $e; // Re-throw to be caught by the caller
        }
    }

    /**
     * Get stock entries by status (for dropdown selection)
     *
     * @param string $status Status filter
     * @return array Stock entries with coil details
     */
    public function getByStatus($status)
    {
        try {
            $sql = "SELECT se.*, 
                       c.code as coil_code, 
                       c.name as coil_name, 
                       c.color as coil_color,
                       c.net_weight as coil_weight,
                       c.category as coil_category
                FROM {$this->table} se
                INNER JOIN coils c ON se.coil_id = c.id
                WHERE se.status = :status 
                AND se.meters_remaining > 0
                AND se.deleted_at IS NULL
                AND c.deleted_at IS NULL
                ORDER BY se.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':status' => $status]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Stock entry fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total meters for a coil
     *
     * @param int $coilId Coil ID
     * @return float
     */
    public function getTotalMeters($coilId)
    {
        try {
            $sql = "SELECT SUM(meters) as total 
                    FROM {$this->table} 
                    WHERE coil_id = :coil_id AND deleted_at IS NULL";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            $result = $stmt->fetch();

            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log('Total meters fetch error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check and update coil status based on stock entries
     * Updates coil to "out_of_stock" if all entries are depleted
     *
     * @param int $coilId Coil ID
     * @return bool
     */
    public function checkAndUpdateCoilStatus($coilId)
    {
        try {
            // Check if any stock entries have remaining meters
            $sql = "SELECT SUM(meters_remaining) as total_remaining 
                FROM {$this->table} 
                WHERE coil_id = :coil_id 
                AND deleted_at IS NULL";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            $result = $stmt->fetch();

            $totalRemaining = $result['total_remaining'] ?? 0;

            // Update coil status if no meters remaining
            if ($totalRemaining <= 0) {
                $coilSql = "UPDATE coils 
                       SET status = :status, updated_at = NOW() 
                       WHERE id = :id";

                $coilStmt = $this->db->prepare($coilSql);
                return $coilStmt->execute([
                    ':id' => $coilId,
                    ':status' => STOCK_STATUS_OUT_OF_STOCK,
                ]);
            }

            return true;
        } catch (PDOException $e) {
            error_log('Coil status update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total remaining meters for a coil
     *
     * @param int $coilId Coil ID
     * @return float
     */
    public function getTotalRemainingForCoil($coilId)
    {
        try {
            $sql = "SELECT SUM(meters_remaining) as total 
                FROM {$this->table} 
                WHERE coil_id = :coil_id AND deleted_at IS NULL";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':coil_id' => $coilId]);
            $result = $stmt->fetch();

            return floatval($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log('Get total remaining error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get stock entries by coil and status
     *
     * @param int $coilId Coil ID
     * @param string $status Status filter
     * @return array
     */
    public function getByCoilAndStatus($coilId, $status)
    {
        try {
            $sql = "SELECT * FROM {$this->table} 
                WHERE coil_id = :coil_id 
                AND status = :status 
                AND meters_remaining > 0
                AND deleted_at IS NULL
                ORDER BY created_at ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':coil_id' => $coilId,
                ':status' => $status,
            ]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Stock entry fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Count stock entries by status
     *
     * @param string $status Status filter (optional)
     * @return int
     */
    public function countByStatus($status = null)
    {
        try {
            if ($status === null) {
                $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
                $stmt = $this->db->query($sql);
            } else {
                $sql = "SELECT COUNT(*) as total 
                        FROM {$this->table} 
                        WHERE status = :status AND deleted_at IS NULL";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':status' => $status]);
            }

            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log('Stock entry count error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total meters by status
     *
     * @param string $status Status filter
     * @return float
     */
    public function getTotalMetersByStatus($status)
    {
        try {
            $sql = "SELECT SUM(meters_remaining) as total 
                    FROM {$this->table} 
                    WHERE status = :status AND deleted_at IS NULL";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':status' => $status]);
            $result = $stmt->fetch();

            return floatval($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log('Total meters by status fetch error: ' . $e->getMessage());
            return 0;
        }
    }

 public function updateStatusAndUsageWithWeight($id, $status, $metersUsed, $weightKgUsed)
{
    try {
        // First check if meters_used column exists
        $checkColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'meters_used'");
        if ($checkColumn->rowCount() === 0) {
            // Add column if it doesn't exist
            $this->db->exec(
                "ALTER TABLE {$this->table} ADD COLUMN meters_used DECIMAL(10,2) DEFAULT 0.00 AFTER meters_remaining"
            );
        }
        
        $sql = "UPDATE {$this->table} 
                SET status = :status,
                    meters_used = COALESCE(meters_used, 0) + :meters_used,
                    meters_remaining = meters_remaining - :meters_to_deduct,
                    weight_kg_remaining = weight_kg_remaining - :weight_kg_used,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':status' => $status,
            ':meters_used' => $metersUsed,
            ':meters_to_deduct' => $metersUsed,  // ← Different placeholder name
            ':weight_kg_used' => $weightKgUsed,
            ':id' => $id,
        ]);

        if (!$result) {
            error_log('Stock entry weight update failed: ' . print_r($stmt->errorInfo(), true));
            return false;
        }

        return true;
        
    } catch (PDOException $e) {
        error_log('Stock entry weight update error: ' . $e->getMessage());
        error_log('Parameters: ID=' . $id . ', Status=' . $status . ', Meters=' . $metersUsed . ', Weight KG=' . $weightKgUsed);
        return false;
    }
}
}
