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

    /**
     * Create a new receipt
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (invoice_id, amount_paid, payment_method, reference, created_by) 
                VALUES (:invoice_id, :amount_paid, :payment_method, :reference, :created_by)";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':invoice_id' => $data['invoice_id'],
            ':amount_paid' => $data['amount_paid'],
            ':payment_method' => $data['payment_method'] ?? 'cash',
            ':reference' => $data['reference'] ?? null,
            ':created_by' => $data['created_by'],
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Get all receipts with optional filters
     */
    public function getAll($limit = 10, $offset = 0, $status = '', $invoiceId = '')
    {
        $sql = "SELECT 
                    r.*, 
                    i.invoice_number, 
                    i.total as invoice_total,
                    i.status as invoice_status,
                    JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name')) as customer_name,
                    JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.phone')) as customer_phone,
                    u.name as created_by_name
                FROM {$this->table} r
                INNER JOIN invoices i ON r.invoice_id = i.id
                LEFT JOIN users u ON r.created_by = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($invoiceId)) {
            $sql .= ' AND r.invoice_id = :invoice_id';
            $params[':invoice_id'] = $invoiceId;
        }

        $sql .= ' ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset';
        $params[':limit'] = (int) $limit;
        $params[':offset'] = (int) $offset;

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total number of receipts with optional filters
     */
    public function count($status = '', $invoiceId = '')
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} r
                INNER JOIN invoices i ON r.invoice_id = i.id
                WHERE 1=1";

        $params = [];

        if (!empty($invoiceId)) {
            $sql .= ' AND r.invoice_id = :invoice_id';
            $params[':invoice_id'] = $invoiceId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Get unique invoice numbers for filter dropdown
     */
    public function getInvoicesForFilter()
    {
        $sql = "SELECT DISTINCT i.id, i.invoice_number,
                JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name')) as customer_name
                FROM invoices i
                INNER JOIN {$this->table} r ON r.invoice_id = i.id
                ORDER BY i.invoice_number DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a receipt by ID with related data
     */
    public function findById($id)
    {
        $sql = "SELECT 
                r.*, 
                i.invoice_number,
                i.total as invoice_total,
                i.paid_amount as invoice_paid,
                i.status as invoice_status,
                i.invoice_shape,
                u.name as created_by_name
            FROM {$this->table} r
            INNER JOIN invoices i ON r.invoice_id = i.id
            LEFT JOIN users u ON r.created_by = u.id
            WHERE r.id = :id
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($receipt && isset($receipt['invoice_shape'])) {
            $invoiceShape = json_decode($receipt['invoice_shape'], true);
            $receipt['customer_name'] = $invoiceShape['customer']['name'] ?? null;
            $receipt['customer_phone'] = $invoiceShape['customer']['phone'] ?? null;
        }

        return $receipt ?: null;
    }

    /**
     * Get all receipts for a specific invoice
     */
    public function findByInvoiceId($invoiceId)
    {
        $sql = "SELECT r.*, u.name as created_by_name
                FROM {$this->table} r
                LEFT JOIN users u ON r.created_by = u.id
                WHERE r.invoice_id = :invoice_id
                ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':invoice_id' => $invoiceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Format a date string to a more readable format
     */
    public function formatDateTime($dateTime, $format = 'M j, Y h:i A')
    {
        if (empty($dateTime) || $dateTime === '0000-00-00 00:00:00') {
            return 'N/A';
        }

        $date = new DateTime($dateTime);
        return $date->format($format);
    }
}
