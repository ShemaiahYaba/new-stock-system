<?php
require_once __DIR__ . '/../config/db.php';

class Receipt
{
    private $db;
    private $table = 'receipts';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (invoice_id, amount_paid, reference, payment_method, created_by) 
                VALUES (:invoice_id, :amount_paid, :reference, :payment_method, :created_by)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':invoice_id' => $data['invoice_id'],
            ':amount_paid' => $data['amount_paid'],
            ':reference' => $data['reference'] ?? null,
            ':payment_method' => $data['payment_method'] ?? 'cash',
            ':created_by' => $data['created_by'],
        ]);

        return $this->db->lastInsertId();
    }

    public function findById($id)
    {
        $sql = "SELECT r.*, i.invoice_number, u.name as created_by_name
                FROM {$this->table} r
                LEFT JOIN invoices i ON r.invoice_id = i.id
                LEFT JOIN users u ON r.created_by = u.id
                WHERE r.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getByInvoiceId($invoiceId)
    {
        $sql = "SELECT r.*, u.name as created_by_name
                FROM {$this->table} r
                LEFT JOIN users u ON r.created_by = u.id
                WHERE r.invoice_id = :invoice_id
                ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':invoice_id' => $invoiceId]);
        return $stmt->fetchAll();
    }
}
