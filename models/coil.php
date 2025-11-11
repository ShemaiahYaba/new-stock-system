<?php
/**
 * Coil Model
 *
 * Handles all coil-related database operations
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
     *
     * @param array $data Coil data
     * @return int|false Coil ID or false on failure
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (code, name, color_id, net_weight, category, status, created_by, created_at) 
                    VALUES (:code, :name, :color_id, :net_weight, :category, :status, :created_by, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':code' => $data['code'],
                ':name' => $data['name'],
                ':color_id' => $data['color_id'],
                ':net_weight' => $data['net_weight'],
                ':category' => $data['category'],
                ':status' => $data['status'] ?? STOCK_STATUS_AVAILABLE,
                ':created_by' => $data['created_by'],
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Coil creation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find coil by ID
     *
     * @param int $id Coil ID
     * @return array|false
     */
    public function findById($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL";
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
     *
     * @param string $code Coil code
     * @return array|false
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
     *
     * @param string $category Category filter (optional)
     * @return int
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
     *
     * @param string $category Category filter
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getAll($category = null, $limit = RECORDS_PER_PAGE, $offset = 0)
    {
        try {
            $sql = "SELECT c.*, u.name as created_by_name, col.name as color_name, col.code as color_code
                FROM {$this->table} c
                LEFT JOIN users u ON c.created_by = u.id
                LEFT JOIN colors col ON c.color_id = col.id
                WHERE c.deleted_at IS NULL";

            if ($category) {
                $sql .= ' AND c.category = :category';
            }

            $sql .= ' ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset';

            $stmt = $this->db->prepare($sql);

            if ($category) {
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
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
     *
     * @param string $category Category filter
     * @return int
     */
    public function count($category = null)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";

            if ($category) {
                $sql .= ' AND category = :category';
            }

            $stmt = $this->db->prepare($sql);

            if ($category) {
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
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
     * Update coil
     *
     * @param int $id Coil ID
     * @param array $data Coil data
     * @return bool
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

            if (isset($data['color'])) {
                $fields[] = 'color = :color';
                $params[':color'] = $data['color'];
            }

            if (isset($data['net_weight'])) {
                $fields[] = 'net_weight = :net_weight';
                $params[':net_weight'] = $data['net_weight'];
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
     * updated✅
     * Update coil status
     *
     * @param int $id Coil ID
     * @param string $status New status
     * @return bool
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
     *
     * @param int $id Coil ID
     * @return bool
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
     * Search coils
     *
     * @param string $query Search query
     * @param string $category Category filter
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function search($query, $category = null, $limit = RECORDS_PER_PAGE, $offset = 0)
    {
        try {
            $sql = "SELECT c.*, u.name as created_by_name 
                    FROM {$this->table} c
                    LEFT JOIN users u ON c.created_by = u.id
                    WHERE c.deleted_at IS NULL 
                    AND (c.code LIKE :query OR c.name LIKE :query)";

            if ($category) {
                $sql .= ' AND c.category = :category';
            }

            $sql .= ' ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset';

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);

            if ($category) {
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
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
     * updated✅
     * Get coils by status
     *
     * @param string $status Status filter
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
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
     * New✅
     * Get coils by category and status
     *
     * @param string $category Category filter
     * @param string $status Status filter
     * @return array
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
     *
     * @return array
     */
    public function getForDropdown()
    {
        try {
            $sql = "SELECT id, code, name, category, status, color, net_weight 
                    FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    ORDER BY code ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Coil dropdown fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * New✅
     * Get coils with remaining stock meters
     * Joins with stock_entries to show available meters
     *
     * @return array
     */
    public function getWithStockInfo()
    {
        try {
            // First, get all coils with stock info
            $sql = "SELECT c.*, 
                       COALESCE(SUM(se.meters_remaining), 0) as total_remaining_meters,
                       COUNT(se.id) as stock_entry_count
                FROM {$this->table} c
                LEFT JOIN stock_entries se ON c.id = se.coil_id 
                    AND se.deleted_at IS NULL 
                    AND se.meters_remaining > 0
                WHERE c.deleted_at IS NULL
                GROUP BY c.id
                HAVING stock_entry_count > 0
                ORDER BY c.code ASC";

            $stmt = $this->db->query($sql);
            $coils = $stmt->fetchAll();

            // Get stock entries for each coil
            $coilIds = array_column($coils, 'id');
            $entriesByCoil = [];

            if (!empty($coilIds)) {
                $placeholders = rtrim(str_repeat('?,', count($coilIds)), ',');
                $sql = "SELECT * FROM stock_entries 
                        WHERE coil_id IN ($placeholders) 
                        AND deleted_at IS NULL 
                        AND meters_remaining > 0";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($coilIds);
                $entries = $stmt->fetchAll();

                // Group entries by coil_id
                foreach ($entries as $entry) {
                    $entriesByCoil[$entry['coil_id']][] = $entry;
                }
            }

            // Merge stock entries into coils data
            foreach ($coils as &$coil) {
                $coil['stock_entries'] = $entriesByCoil[$coil['id']] ?? [];
            }

            return $coils;
        } catch (PDOException $e) {
            error_log('Coil with stock info fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * New✅
     * Get all colors for dropdown - NEW METHOD
     */
    public function getColorsForDropdown() {
    require_once __DIR__ . '/color.php';
    $colorModel = new Color();
    return $colorModel->getActive();
}
}
