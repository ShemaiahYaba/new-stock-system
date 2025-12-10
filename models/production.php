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
        $sql = "SELECT p.*, w.name as warehouse_name, u.name as created_by_name,
                       i.invoice_shape
                FROM {$this->table} p
                LEFT JOIN warehouses w ON p.warehouse_id = w.id
                LEFT JOIN users u ON p.created_by = u.id
                LEFT JOIN invoices i ON p.sale_id = i.sale_id
                WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();

        if ($result) {
            // Decode production paper if it exists
            if (isset($result['production_paper'])) {
                $productionPaper = is_string($result['production_paper']) 
                    ? json_decode($result['production_paper'], true) 
                    : $result['production_paper'];
                
                // If we have invoice_shape, ensure coil color is set
                if (!empty($result['invoice_shape'])) {
                    $invoiceShape = is_string($result['invoice_shape']) 
                        ? json_decode($result['invoice_shape'], true) 
                        : $result['invoice_shape'];
                    
                    if (isset($invoiceShape['coil']['color']) && 
                        (!isset($productionPaper['coil']['color']) || empty($productionPaper['coil']['color']))) {
                        $productionPaper['coil']['color'] = $invoiceShape['coil']['color'];
                    }
                }
                
                $result['production_paper'] = $productionPaper;
            }
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
    /**
     * Get all productions with optional filters
     * 
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @param string $status Optional status filter
     * @return array Array of production records
     */
    public function getAll($limit = 10, $offset = 0, $status = ''): array
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

    /**
     * Search productions by various fields
     * 
     * @param string $query Search query
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @param string $status Optional status filter
     * @return array Array of matching production records
     */
    public function search($query, $limit = 10, $offset = 0, $status = '')
    {
        $sql = "SELECT p.*, w.name as warehouse_name, 
                       s.invoice_number, s.customer_name,
                       s.coil_code, s.coil_name
                FROM {$this->table} p
                LEFT JOIN warehouses w ON p.warehouse_id = w.id
                LEFT JOIN (
                    SELECT i.sale_id, i.invoice_number, 
                           JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name')) as customer_name,
                           JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.coil.code')) as coil_code,
                           JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.coil.name')) as coil_name
                    FROM invoices i
                ) s ON p.sale_id = s.sale_id
                WHERE 1=1";

        $params = [];
        $searchTerm = "%{$query}%";
        
        // If the search term starts with PR- followed by numbers, extract just the number
        $prNumber = null;
        if (preg_match('/^PR\-(\d+)$/i', $query, $matches)) {
            $prNumber = $matches[1];
        } elseif (is_numeric($query)) {
            // If it's just a number, use it as is
            $prNumber = $query;
        }

        $searchConditions = [];
        
        // Add search conditions
        if ($prNumber !== null) {
            $searchConditions[] = 'p.id = :search_id';
            $params[':search_id'] = $prNumber;
        } else {
            $searchConditions[] = 's.invoice_number LIKE :search_invoice';
            $searchConditions[] = 's.customer_name LIKE :search_customer';
            $searchConditions[] = 's.coil_code LIKE :search_coil_code';
            $searchConditions[] = 's.coil_name LIKE :search_coil_name';
            
            $params[':search_invoice'] = $searchTerm;
            $params[':search_customer'] = $searchTerm;
            $params[':search_coil_code'] = $searchTerm;
            $params[':search_coil_name'] = $searchTerm;
        }

        if (!empty($searchConditions)) {
            $sql .= ' AND (' . implode(' OR ', $searchConditions) . ')';
        }

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
                if (is_string($production['production_paper'])) {
                    $decoded = json_decode($production['production_paper'], true);
                    $production['production_paper'] = is_array($decoded) ? $decoded : [];
                } elseif (!is_array($production['production_paper'])) {
                    $production['production_paper'] = [];
                }
            } else {
                $production['production_paper'] = [];
            }
        }

        return $productions;
    }

    /**
     * Count total number of search results
     * 
     * @param string $query Search query
     * @param string $status Optional status filter
     * @return int Number of matching records
     */
    public function countSearch($query, $status = '')
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} p
                LEFT JOIN (
                    SELECT i.sale_id, i.invoice_number, 
                           JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name')) as customer_name,
                           JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.coil.code')) as coil_code,
                           JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.coil.name')) as coil_name
                    FROM invoices i
                ) s ON p.sale_id = s.sale_id
                WHERE 1=1";

        $params = [];
        $searchTerm = "%{$query}%";
        $searchConditions = [
            'p.id LIKE :search_id',
            's.invoice_number LIKE :search_invoice',
            's.customer_name LIKE :search_customer',
            's.coil_code LIKE :search_coil_code',
            's.coil_name LIKE :search_coil_name'
        ];

        $sql .= ' AND (' . implode(' OR ', $searchConditions) . ')';
        
        $params[':search_id'] = $searchTerm;
        $params[':search_invoice'] = $searchTerm;
        $params[':search_customer'] = $searchTerm;
        $params[':search_coil_code'] = $searchTerm;
        $params[':search_coil_name'] = $searchTerm;

        if (!empty($status)) {
            $sql .= ' AND p.status = :status';
            $params[':status'] = $status;
        }

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Get productions by warehouse ID
     * 
     * @param int $warehouseId Warehouse ID
     * @return array Array of production records
     */
    public function getByWarehouse($warehouseId)
    {
        $sql = "SELECT p.*, 
                       w.name as warehouse_name, 
                       s.invoice_number, 
                       s.customer_name,
                       s.created_at as sale_date
                FROM {$this->table} p
                LEFT JOIN warehouses w ON p.warehouse_id = w.id
                LEFT JOIN (
                    SELECT i.sale_id, i.invoice_number, i.created_at,
                           JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name')) as customer_name
                    FROM invoices i
                ) s ON p.sale_id = s.sale_id
                WHERE p.warehouse_id = :warehouse_id
                ORDER BY p.created_at ASC, p.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':warehouse_id' => $warehouseId]);
        $productions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode JSON fields
        foreach ($productions as &$production) {
            if (isset($production['production_paper'])) {
                if (is_string($production['production_paper'])) {
                    $decoded = json_decode($production['production_paper'], true);
                    $production['production_paper'] = is_array($decoded) ? $decoded : [];
                } elseif (!is_array($production['production_paper'])) {
                    $production['production_paper'] = [];
                }
            } else {
                $production['production_paper'] = [];
            }
        }

        return $productions;
    }
}
