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

    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0)
    {
        $sql = "SELECT p.*, w.name as warehouse_name, u.name as created_by_name
                FROM {$this->table} p
                LEFT JOIN warehouses w ON p.warehouse_id = w.id
                LEFT JOIN users u ON p.created_by = u.id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':status' => $status]);
    }
}
