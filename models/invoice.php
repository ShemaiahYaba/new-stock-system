<?php
require_once __DIR__ . '/../config/db.php';

class Invoice
{
    private $db;
    private $table = 'invoices';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data)
    {
        $hash = $this->generateImmutableHash($data);
        $invoiceNumber = $this->generateInvoiceNumber();

        $sql = "INSERT INTO {$this->table} 
                (sale_id, production_id, invoice_number, invoice_shape, total, tax, shipping, 
                 paid_amount, status, immutable_hash) 
                VALUES (:sale_id, :production_id, :invoice_number, :invoice_shape, :total, 
                        :tax, :shipping, :paid_amount, :status, :hash)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':sale_id' => $data['sale_id'],
            ':production_id' => $data['production_id'] ?? null,
            ':invoice_number' => $invoiceNumber,
            ':invoice_shape' => json_encode($data['invoice_shape']),
            ':total' => $data['total'],
            ':tax' => $data['tax'] ?? 0,
            ':shipping' => $data['shipping'] ?? 0,
            ':paid_amount' => $data['paid_amount'] ?? 0,
            ':status' => $data['status'] ?? INVOICE_STATUS_UNPAID,
            ':hash' => $hash,
        ]);

        return $this->db->lastInsertId();
    }

    private function generateInvoiceNumber()
    {
        $prefix = 'INV-' . date('Y');
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE invoice_number LIKE :prefix";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':prefix' => $prefix . '%']);
        $result = $stmt->fetch();
        $count = $result['count'] + 1;

        return $prefix . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    private function generateImmutableHash($data)
    {
        return hash('sha256', json_encode($data) . time());
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();

        if ($result && isset($result['invoice_shape'])) {
            $result['invoice_shape'] = json_decode($result['invoice_shape'], true);
        }

        return $result;
    }

    public function findBySaleId($saleId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE sale_id = :sale_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sale_id' => $saleId]);
        return $stmt->fetchAll();
    }

    public function updatePaidAmount($id, $amount)
    {
        $sql = "SELECT paid_amount, total FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $invoice = $stmt->fetch();

        if (!$invoice) {
            return false;
        }

        $newPaid = $invoice['paid_amount'] + $amount;
        $newStatus = $newPaid >= $invoice['total'] ? INVOICE_STATUS_PAID : INVOICE_STATUS_PARTIAL;

        $sql = "UPDATE {$this->table} 
                SET paid_amount = :paid, status = :status, updated_at = NOW() 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':paid' => $newPaid,
            ':status' => $newStatus,
        ]);
    }
}
