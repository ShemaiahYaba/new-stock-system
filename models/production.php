<?php
require_once __DIR__ . '/../config/db.php';

class Production
{
    private $db;
    private $table = 'production';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data)
    {
        $hash = $this->generateImmutableHash($data);

        $sql = "INSERT INTO {$this->table} 
                (sale_id, warehouse_id, production_paper, status, created_by, immutable_hash) 
                VALUES (:sale_id, :warehouse_id, :production_paper, :status, :created_by, :hash)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':sale_id' => $data['sale_id'],
            ':warehouse_id' => $data['warehouse_id'],
            ':production_paper' => json_encode($data['production_paper']),
            ':status' => $data['status'] ?? PRODUCTION_STATUS_PENDING,
            ':created_by' => $data['created_by'],
            ':hash' => $hash,
        ]);

        return $this->db->lastInsertId();
    }

    private function generateImmutableHash($data)
    {
        $hashData = json_encode($data) . time() . rand();
        return hash('sha256', $hashData);
    }

    public function findById($id)
    {
        $sql = "SELECT p.*, w.name as warehouse_name, u.name as created_by_name
                FROM {$this->table} p
                LEFT JOIN warehouses w ON p.warehouse_id = w.id
                LEFT JOIN users u ON p.created_by = u.id
                WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();

        if ($result && isset($result['production_paper'])) {
            $result['production_paper'] = json_decode($result['production_paper'], true);
        }

        return $result;
    }
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':status' => $status]);
    }

    /**
     * Find production record by sale ID
     *
     * @param int $saleId The sale ID to search for
     * @return array|null The production record or null if not found
     */
    public function findBySaleId($saleId)
    {
        $sql = "SELECT p.*, w.name as warehouse_name 
                FROM {$this->table} p
                LEFT JOIN warehouses w ON p.warehouse_id = w.id
                WHERE p.sale_id = :sale_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sale_id' => $saleId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['production_paper'])) {
            $result['production_paper'] = json_decode($result['production_paper'], true);
        }

        return $result ?: null;
    }

    /**
     * Get all productions with optional filters
     */
    public function getAll($limit = 10, $offset = 0, $status = '')
    {
        $sql = "SELECT p.*, w.name as warehouse_name, 
                       s.invoice_number, s.customer_name
                FROM {$this->table} p
                LEFT JOIN warehouses w ON p.warehouse_id = w.id
                LEFT JOIN (
                    SELECT i.sale_id, i.invoice_number, 
                           JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name')) as customer_name
                    FROM invoices i
                ) s ON p.sale_id = s.sale_id
                WHERE 1=1";

        $params = [];

        if (!empty($status)) {
            $sql .= ' AND p.status = :status';
            $params[':status'] = $status;
        }

        $sql .= ' ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset';
        $params[':limit'] = (int) $limit;
        $params[':offset'] = (int) $offset;

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }

        $stmt->execute();
        $productions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode JSON fields
        foreach ($productions as &$production) {
            if (isset($production['production_paper'])) {
                // If it's already an array, use it as is
                if (is_array($production['production_paper'])) {
                    continue;
                }
                // If it's a string, try to decode it
                if (is_string($production['production_paper'])) {
                    $decoded = json_decode($production['production_paper'], true);
                    $production['production_paper'] = is_array($decoded) ? $decoded : [];
                } else {
                    $production['production_paper'] = [];
                }
            } else {
                $production['production_paper'] = [];
            }
        }

        return $productions;
    }

    /**
     * Count total number of productions with optional filters
     */
    public function count($status = '')
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($status)) {
            $sql .= ' AND status = :status';
            $params[':status'] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int) $result['total'] : 0;
    }
}
