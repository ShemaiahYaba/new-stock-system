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
                (sale_type, sale_reference_id, sale_id, production_id, invoice_number, 
                 invoice_shape, total, tax, shipping, paid_amount, status, immutable_hash) 
                VALUES (:sale_type, :sale_reference_id, :sale_id, :production_id, 
                        :invoice_number, :invoice_shape, :total, :tax, :shipping, 
                        :paid_amount, :status, :hash)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':sale_type' => $data['sale_type'] ?? null,
            ':sale_reference_id' => $data['sale_reference_id'] ?? null,
            ':sale_id' => $data['sale_id'] ?? null,
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

    /**
     * Find invoice by tile sale ID (NEW METHOD)
     */
    public function findByTileSale($tileSaleId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE sale_type = 'tile_sale' 
                AND sale_reference_id = :tile_sale_id 
                ORDER BY created_at DESC 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tile_sale_id' => $tileSaleId]);
        $result = $stmt->fetch();

        if ($result && isset($result['invoice_shape'])) {
            $result['invoice_shape'] = json_decode($result['invoice_shape'], true);
        }

        return $result;
    }

    public function getAll($limit = 10, $offset = 0, $status = '')
    {
        // First, get basic invoice data
        $sql = "SELECT i.* FROM {$this->table} i WHERE 1=1";

        $params = [];

        if (!empty($status)) {
            $sql .= ' AND i.status = :status';
            $params[':status'] = $status;
        }

        $sql .= ' ORDER BY i.created_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);

        // Bind parameters
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        // Bind status parameter if set
        if (!empty($status)) {
            $stmt->bindValue(':status', $status);
        }

        $stmt->execute();
        $results = $stmt->fetchAll();

        // Process each invoice to add customer data from invoice_shape
        foreach ($results as &$row) {
            if (isset($row['invoice_shape'])) {
                $invoiceData = json_decode($row['invoice_shape'], true);
                $row['invoice_shape'] = $invoiceData;

                // Add customer data directly from invoice_shape
                if (isset($invoiceData['customer'])) {
                    $row['customer_name'] = $invoiceData['customer']['name'] ?? 'N/A';
                    $row['customer_phone'] = $invoiceData['customer']['phone'] ?? '';
                } else {
                    $row['customer_name'] = 'N/A';
                    $row['customer_phone'] = '';
                }

                // Add sale reference if available
                if (isset($invoiceData['meta']['ref'])) {
                    $row['sale_reference'] = $invoiceData['meta']['ref'];
                } else {
                    $row['sale_reference'] = 'N/A';
                }
            } else {
                $row['customer_name'] = 'N/A';
                $row['customer_phone'] = '';
                $row['sale_reference'] = 'N/A';
                $row['invoice_shape'] = [];
            }
        }

        return $results;
    }

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

        $result = $stmt->fetch();
        return (int) $result['total'];
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
