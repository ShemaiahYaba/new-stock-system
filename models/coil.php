<?php
/**
 * Coil Model - FIXED Parameter Binding
 * Replace models/coil.php with this entire file
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class Coil
{
    private $db;
    private $table = 'coils';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new coil
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table}
                    (code, name, color_id, net_weight, meters, gauge, pallet_size, kzinc_track_mode, category, status, created_by, created_at)
                    VALUES (:code, :name, :color_id, :net_weight, :meters, :gauge, :pallet_size, :kzinc_track_mode, :category, :status, :created_by, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':code'             => $data['code'],
                ':name'             => $data['name'],
                ':color_id'         => $data['color_id'],
                ':net_weight'       => $data['net_weight'],
                ':meters'           => $data['meters'] ?? null,
                ':gauge'            => $data['gauge'] ?? null,
                ':pallet_size'      => $data['pallet_size'] ?? null,
                ':kzinc_track_mode' => $data['kzinc_track_mode'] ?? null,
                ':category'         => $data['category'],
                ':status'           => $data['status'] ?? STOCK_STATUS_AVAILABLE,
                ':created_by'       => $data['created_by'],
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Coil creation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find coil by ID
     */
    public function findById($id)
    {
        try {
            $sql = "SELECT c.*, col.name as color_name, col.code as color_code, col.hex_code as color_hex 
                    FROM {$this->table} c
                    LEFT JOIN colors col ON c.color_id = col.id
                    WHERE c.id = :id AND c.deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Coil find error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find coil by code
     */
    public function findByCode($code)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE code = :code AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':code' => $code]);

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Coil find error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Count coils by category
     */
    public function countByCategory($category = null)
    {
        try {
            if ($category === null) {
                $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
                $stmt = $this->db->query($sql);
            } else {
                $sql = "SELECT COUNT(*) as total 
                    FROM {$this->table} 
                    WHERE category = :category AND deleted_at IS NULL";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':category' => $category]);
            }

            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log('Coil count by category error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all coils with pagination
     */
    public function getAll($category = null, $limit = RECORDS_PER_PAGE, $offset = 0, $status = null, $gauge = null, $colorId = null)
    {
        try {
            $sql = "SELECT c.*, u.name as created_by_name, col.name as color_name, col.code as color_code, col.hex_code as color_hex
                    FROM {$this->table} c
                    LEFT JOIN users u ON c.created_by = u.id
                    LEFT JOIN colors col ON c.color_id = col.id
                    WHERE c.deleted_at IS NULL";

            if ($category) {
                $sql .= ' AND c.category = :category';
            }
            if ($status) {
                $sql .= ' AND c.status = :status';
            }
            if ($gauge) {
                $sql .= ' AND c.gauge = :gauge';
            }
            if ($colorId) {
                $sql .= ' AND c.color_id = :color_id';
            }

            $sql .= ' ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset';

            $stmt = $this->db->prepare($sql);

            if ($category) {
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
            }
            if ($status) {
                $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            }
            if ($gauge) {
                $stmt->bindValue(':gauge', $gauge, PDO::PARAM_STR);
            }
            if ($colorId) {
                $stmt->bindValue(':color_id', (int)$colorId, PDO::PARAM_INT);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Coil fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Count total coils
     */
    public function count($category = null, $status = null, $gauge = null, $colorId = null)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";

            if ($category) {
                $sql .= ' AND category = :category';
            }
            if ($status) {
                $sql .= ' AND status = :status';
            }
            if ($gauge) {
                $sql .= ' AND gauge = :gauge';
            }
            if ($colorId) {
                $sql .= ' AND color_id = :color_id';
            }

            $stmt = $this->db->prepare($sql);

            if ($category) {
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
            }
            if ($status) {
                $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            }
            if ($gauge) {
                $stmt->bindValue(':gauge', $gauge, PDO::PARAM_STR);
            }
            if ($colorId) {
                $stmt->bindValue(':color_id', (int)$colorId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $result = $stmt->fetch();

            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log('Coil count error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get distinct gauge values for filter dropdowns
     */
    public function getDistinctGauges($category = null)
    {
        try {
            $sql = "SELECT DISTINCT gauge FROM {$this->table}
                    WHERE deleted_at IS NULL AND gauge IS NOT NULL AND gauge != ''";
            if ($category) {
                $sql .= ' AND category = :category';
            }
            $sql .= ' ORDER BY gauge ASC';

            $stmt = $this->db->prepare($sql);
            if ($category) {
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
            }
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log('Coil distinct gauges error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update coil
     */
    public function update($id, $data)
    {
        try {
            $fields = [];
            $params = [':id' => $id];

            if (isset($data['code'])) {
                $fields[] = 'code = :code';
                $params[':code'] = $data['code'];
            }

            if (isset($data['name'])) {
                $fields[] = 'name = :name';
                $params[':name'] = $data['name'];
            }

            if (isset($data['color_id'])) {
                $fields[] = 'color_id = :color_id';
                $params[':color_id'] = $data['color_id'];
            }

            if (isset($data['category'])) {
                $fields[] = 'category = :category';
                $params[':category'] = $data['category'];
            }

            if (isset($data['net_weight'])) {
                $fields[] = 'net_weight = :net_weight';
                $params[':net_weight'] = $data['net_weight'];
            }

            if (isset($data['meters'])) {
                $fields[] = 'meters = :meters';
                $params[':meters'] = $data['meters'];
            }

            if (isset($data['gauge'])) {
                $fields[] = 'gauge = :gauge';
                $params[':gauge'] = $data['gauge'];
            }

            if (array_key_exists('pallet_size', $data)) {
                $fields[] = 'pallet_size = :pallet_size';
                $params[':pallet_size'] = $data['pallet_size'];
            }

            if (array_key_exists('kzinc_track_mode', $data)) {
                $fields[] = 'kzinc_track_mode = :kzinc_track_mode';
                $params[':kzinc_track_mode'] = $data['kzinc_track_mode'];
            }

            if (isset($data['status'])) {
                $fields[] = 'status = :status';
                $params[':status'] = $data['status'];
            }

            if (empty($fields)) {
                return false;
            }

            $fields[] = 'updated_at = NOW()';

            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . ' WHERE id = :id';
            $stmt = $this->db->prepare($sql);

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Coil update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update coil status
     */
    public function updateStatus($id, $status)
    {
        try {
            $sql = "UPDATE {$this->table} 
                SET status = :status, updated_at = NOW() 
                WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':status' => $status,
            ]);
        } catch (PDOException $e) {
            error_log('Coil status update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft delete coil
     */
    public function delete($id)
    {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Coil delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Search coils - FIXED with proper parameter binding
     * Since utf8mb4_unicode_ci is case-insensitive, LOWER() is optional
     * but we'll use it for consistency across different collations
     */
    public function search($query, $category = null, $limit = RECORDS_PER_PAGE, $offset = 0, $status = null, $gauge = null, $colorId = null)
    {
        try {
            $searchQuery = "%{$query}%";

            $sql = "SELECT c.*, u.name as created_by_name, col.name as color_name, col.code as color_code, col.hex_code as color_hex
                    FROM {$this->table} c
                    LEFT JOIN users u ON c.created_by = u.id
                    LEFT JOIN colors col ON c.color_id = col.id
                    WHERE c.deleted_at IS NULL
                    AND (c.code LIKE :query1 OR c.name LIKE :query2)";

            if ($category) {
                $sql .= ' AND c.category = :category';
            }
            if ($status) {
                $sql .= ' AND c.status = :status';
            }
            if ($gauge) {
                $sql .= ' AND c.gauge = :gauge';
            }
            if ($colorId) {
                $sql .= ' AND c.color_id = :color_id';
            }

            $sql .= ' ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset';

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':query1', $searchQuery, PDO::PARAM_STR);
            $stmt->bindValue(':query2', $searchQuery, PDO::PARAM_STR);

            if ($category) {
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
            }
            if ($status) {
                $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            }
            if ($gauge) {
                $stmt->bindValue(':gauge', $gauge, PDO::PARAM_STR);
            }
            if ($colorId) {
                $stmt->bindValue(':color_id', (int)$colorId, PDO::PARAM_INT);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Coil search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get coils by status
     */
    public function getByStatus($status, $limit = 1000, $offset = 0)
    {
        try {
            $sql = "SELECT * FROM {$this->table} 
                WHERE status = :status 
                AND deleted_at IS NULL
                ORDER BY code ASC
                LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Coil fetch by status error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get coils by category and status
     */
    public function getByCategoryAndStatus($category, $status)
    {
        try {
            $sql = "SELECT * FROM {$this->table} 
                WHERE category = :category 
                AND status = :status 
                AND deleted_at IS NULL
                ORDER BY code ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':category' => $category,
                ':status' => $status,
            ]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Coil fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all active coils for dropdown selection
     */
    public function getForDropdown()
    {
        try {
            $sql = "SELECT c.id, c.code, c.name, c.category, c.status, c.color_id, c.net_weight, c.meters, c.gauge,
                           col.name as color_name, col.hex_code as color_hex
                    FROM {$this->table} c
                    LEFT JOIN colors col ON c.color_id = col.id
                    WHERE c.deleted_at IS NULL
                    ORDER BY c.code ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Coil dropdown fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get coils that have at least one factory_use stock entry with meters remaining.
     * Used by the New Sale workflow so the coil dropdown only shows coils
     * the user can actually select a stock entry for.
     */
    public function getForSaleDropdown()
    {
        try {
            $sql = "SELECT c.id, c.code, c.name, c.category, c.status, c.color_id,
                           c.net_weight, c.meters, c.gauge, c.kzinc_track_mode,
                           col.name as color_name, col.hex_code as color_hex
                    FROM {$this->table} c
                    LEFT JOIN colors col ON c.color_id = col.id
                    WHERE c.deleted_at IS NULL
                      AND c.category != :tile
                      AND EXISTS (
                          SELECT 1 FROM stock_entries se
                          WHERE se.coil_id = c.id
                            AND se.deleted_at IS NULL
                            AND se.status = 'factory_use'
                            AND se.meters_remaining > 0
                            AND (se.unit_type = 'meters' OR se.unit_type IS NULL)
                      )
                    ORDER BY c.code ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tile' => STOCK_CATEGORY_TILE]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Coil sale dropdown fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get coils with remaining stock meters
     */
    public function getWithStockInfo()
    {
        try {
            $sql = "SELECT c.*,
        col.name as color_name,
        COALESCE(SUM(se.meters_remaining), 0) as total_remaining_meters,
        COALESCE(SUM(se.pieces_remaining), 0) as total_remaining_pieces,
        COUNT(se.id) as stock_entry_count
        FROM {$this->table} c
        LEFT JOIN colors col ON c.color_id = col.id
        LEFT JOIN stock_entries se ON c.id = se.coil_id
            AND se.deleted_at IS NULL
            AND (se.meters_remaining > 0 OR se.pieces_remaining > 0)
        WHERE c.deleted_at IS NULL
        GROUP BY c.id
        HAVING stock_entry_count > 0
        ORDER BY c.code ASC";

            $stmt = $this->db->query($sql);
            $coils = $stmt->fetchAll();

            $coilIds = array_column($coils, 'id');
            $entriesByCoil = [];

            if (!empty($coilIds)) {
                $placeholders = rtrim(str_repeat('?,', count($coilIds)), ',');
                $sql = "SELECT * FROM stock_entries
                        WHERE coil_id IN ($placeholders)
                        AND deleted_at IS NULL
                        AND (meters_remaining > 0 OR pieces_remaining > 0)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($coilIds);
                $entries = $stmt->fetchAll();

                foreach ($entries as $entry) {
                    $entriesByCoil[$entry['coil_id']][] = $entry;
                }
            }

            foreach ($coils as &$coil) {
                $coil['stock_entries'] = $entriesByCoil[$coil['id']] ?? [];
            }

            return $coils;
        } catch (PDOException $e) {
            error_log('Coil with stock info fetch error: ' . $e->getMessage());
            return [];
        }
    }
}