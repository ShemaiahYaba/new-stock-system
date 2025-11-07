<?php
/**
 * Supply Model
 * Handles all database operations for supply/delivery records
 */
class Supply
{
    private $db;
    private $table = 'supply_delivery'; // Changed from 'supplies' to 'supply_delivery'

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all supply records with optional status filter
     */
    public function getAll($limit = 10, $offset = 0, $status = '')
    {
        $sql = "SELECT s.*, 
                       p.sale_id,
                       c.name as customer_name,
                       w.name as warehouse_name
                FROM {$this->table} s
                LEFT JOIN production p ON s.production_id = p.id
                LEFT JOIN sales sl ON p.sale_id = sl.id
                LEFT JOIN customers c ON sl.customer_id = c.id
                LEFT JOIN warehouses w ON s.warehouse_id = w.id
                WHERE 1=1";

        $params = [];

        if (!empty($status)) {
            $sql .= ' AND s.status = :status';
            $params[':status'] = $status;
        }

        $sql .= ' ORDER BY s.created_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);

        // Bind parameters
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        if (!empty($status)) {
            $stmt->bindValue(':status', $status);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total supply records with optional status filter
     */
    public function count($status = '')
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];

        if (!empty($status)) {
            $sql .= ' WHERE status = :status';
            $params[':status'] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    }

    /**
     * Get supply record by ID
     */
    public function getById($id)
    {
        $sql = "SELECT s.*, 
                       p.sale_id,
                       c.name as customer_name,
                       c.phone as customer_phone,
                       w.name as warehouse_name
                FROM {$this->table} s
                LEFT JOIN production p ON s.production_id = p.id
                LEFT JOIN sales sl ON p.sale_id = sl.id
                LEFT JOIN customers c ON sl.customer_id = c.id
                LEFT JOIN warehouses w ON s.warehouse_id = w.id
                WHERE s.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update supply status
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} 
                SET status = :status, 
                    updated_at = NOW() 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status,
        ]);
    }

    /**
     * Create a new supply record
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (production_id, warehouse_id, status, notes, created_at)
                VALUES (:production_id, :warehouse_id, :status, :notes, NOW())";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':production_id' => $data['production_id'],
            ':warehouse_id' => $data['warehouse_id'],
            ':status' => $data['status'] ?? 'pending',
            ':notes' => $data['notes'] ?? null,
        ]);

        if ($result) {
            return $this->db->lastInsertId();
        }

        return false;
    }
}
