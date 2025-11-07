<?php
require_once __DIR__ . '/../config/db.php';

class SupplyDelivery
{
    private $db;
    private $table = 'supply_delivery';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (production_id, warehouse_id, status, notes) 
                VALUES (:production_id, :warehouse_id, :status, :notes)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':production_id' => $data['production_id'],
            ':warehouse_id' => $data['warehouse_id'],
            ':status' => $data['status'] ?? SUPPLY_STATUS_PENDING,
            ':notes' => $data['notes'] ?? null,
        ]);

        return $this->db->lastInsertId();
    }

    public function updateStatus($id, $status)
    {
        $fields = ['status = :status', 'updated_at = NOW()'];
        $params = [':id' => $id, ':status' => $status];

        if ($status === SUPPLY_STATUS_SUPPLIED) {
            $fields[] = 'delivered_at = NOW()';
        } elseif ($status === SUPPLY_STATUS_RETURNED) {
            $fields[] = 'return_requested_at = NOW()';
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getByProductionId($productionId)
    {
        $sql = "SELECT s.*, w.name as warehouse_name
                FROM {$this->table} s
                LEFT JOIN warehouses w ON s.warehouse_id = w.id
                WHERE s.production_id = :production_id
                ORDER BY s.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':production_id' => $productionId]);
        return $stmt->fetchAll();
    }
}
