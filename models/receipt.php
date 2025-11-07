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
     * Get all receipts with optional filters
     */
    public function getAll($limit = 10, $offset = 0, $status = '', $invoiceId = '')
    {
        $sql = "SELECT 
                    r.*, 
                    i.invoice_number, 
                    i.status as invoice_status,
                    JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name')) as customer_name,
                    JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.phone')) as customer_phone
                FROM {$this->table} r
                INNER JOIN invoices i ON r.invoice_id = i.id
                WHERE 1=1";

        $params = [];

        if (!empty($status)) {
            $sql .= ' AND r.status = :status';
            $params[':status'] = $status;
        }

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

        if (!empty($status)) {
            $sql .= ' AND r.status = :status';
            $params[':status'] = $status;
        }

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
        $sql = "SELECT DISTINCT i.id, i.invoice_number 
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
                i.status as invoice_status,
                i.invoice_shape->>'$.customer.name' as customer_name,
                i.invoice_shape->>'$.customer.phone' as customer_phone,
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
            unset($receipt['invoice_shape']);
        }

        return $receipt ?: null;
    }

    /**
     * Format a date string to a more readable format
     *
     * @param string $dateTime The date string to format
     * @param string $format The format to use (default: 'M j, Y h:i A')
     * @return string Formatted date string
     */
    public function formatDateTime($dateTime, $format = 'M j, Y h:i A')
    {
        if (empty($dateTime) || $dateTime === '0000-00-00 00:00:00') {
            return 'N/A';
        }

        $date = new DateTime($dateTime);
        return $date->format($format);
    }
    // Add other methods like findById, create, update, delete as needed
}
