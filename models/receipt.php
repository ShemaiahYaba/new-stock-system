<?php
/**
 * Enhanced Receipt Model with Fixed Filtering
 * File: models/receipt.php
 * FIXED: Follows the same parameter binding pattern as coil.php and customer.php
 */
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
     * Get all receipts with advanced filters
     * FIXED: Uses unique parameter names like coil.php search method
     */
    public function getAllWithFilters($limit = 10, $offset = 0, $filters = [])
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

        // Apply filters
        if (!empty($filters['invoice_id'])) {
            $sql .= ' AND r.invoice_id = :invoice_id';
            $params[':invoice_id'] = $filters['invoice_id'];
        }

        if (!empty($filters['payment_method'])) {
            $sql .= ' AND r.payment_method = :payment_method';
            $params[':payment_method'] = $filters['payment_method'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= ' AND DATE(r.created_at) >= :date_from';
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= ' AND DATE(r.created_at) <= :date_to';
            $params[':date_to'] = $filters['date_to'];
        }

        // FIXED: Customer search with unique parameter names (like coil.php pattern)
        // Handles both lowercase and capitalized JSON keys
        if (!empty($filters['customer_search'])) {
            $searchTerm = '%' . strtolower($filters['customer_search']) . '%';
            $sql .= ' AND (
                LOWER(JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, "$.customer.name"))) LIKE :search_name
                OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, "$.Customer.Name"))) LIKE :search_name2
                OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, "$.customer.phone"))) LIKE :search_phone
                OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, "$.Customer.Phone"))) LIKE :search_phone2
                OR LOWER(i.invoice_number) LIKE :search_invoice
            )';
            $params[':search_name'] = $searchTerm;
            $params[':search_name2'] = $searchTerm;
            $params[':search_phone'] = $searchTerm;
            $params[':search_phone2'] = $searchTerm;
            $params[':search_invoice'] = $searchTerm;
        }

        if (!empty($filters['min_amount'])) {
            $sql .= ' AND r.amount_paid >= :min_amount';
            $params[':min_amount'] = $filters['min_amount'];
        }

        if (!empty($filters['max_amount'])) {
            $sql .= ' AND r.amount_paid <= :max_amount';
            $params[':max_amount'] = $filters['max_amount'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND i.status = :status';
            $params[':status'] = $filters['status'];
        }

        $sql .= ' ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);

        // Bind all parameters with proper types
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count receipts with filters
     * FIXED: Same parameter naming pattern
     */
    public function countWithFilters($filters = [])
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} r
                INNER JOIN invoices i ON r.invoice_id = i.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['invoice_id'])) {
            $sql .= ' AND r.invoice_id = :invoice_id';
            $params[':invoice_id'] = $filters['invoice_id'];
        }

        if (!empty($filters['payment_method'])) {
            $sql .= ' AND r.payment_method = :payment_method';
            $params[':payment_method'] = $filters['payment_method'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= ' AND DATE(r.created_at) >= :date_from';
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= ' AND DATE(r.created_at) <= :date_to';
            $params[':date_to'] = $filters['date_to'];
        }

        // FIXED: Unique parameter names - handles both lowercase and capitalized JSON keys
        if (!empty($filters['customer_search'])) {
            $searchTerm = '%' . strtolower($filters['customer_search']) . '%';
            $sql .= ' AND (
                LOWER(JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, "$.customer.name"))) LIKE :count_name
                OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, "$.Customer.Name"))) LIKE :count_name2
                OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, "$.customer.phone"))) LIKE :count_phone
                OR LOWER(JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, "$.Customer.Phone"))) LIKE :count_phone2
                OR LOWER(i.invoice_number) LIKE :count_invoice
            )';
            $params[':count_name'] = $searchTerm;
            $params[':count_name2'] = $searchTerm;
            $params[':count_phone'] = $searchTerm;
            $params[':count_phone2'] = $searchTerm;
            $params[':count_invoice'] = $searchTerm;
        }

        if (!empty($filters['min_amount'])) {
            $sql .= ' AND r.amount_paid >= :min_amount';
            $params[':min_amount'] = $filters['min_amount'];
        }

        if (!empty($filters['max_amount'])) {
            $sql .= ' AND r.amount_paid <= :max_amount';
            $params[':max_amount'] = $filters['max_amount'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND i.status = :status';
            $params[':status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Get filter options (payment methods, invoices, etc.)
     */
    public function getFilterOptions()
    {
        // Get unique payment methods
        $paymentMethodsSql = "SELECT DISTINCT payment_method 
                              FROM {$this->table} 
                              ORDER BY payment_method";
        $stmt = $this->db->prepare($paymentMethodsSql);
        $stmt->execute();
        $paymentMethods = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Get invoices with receipts
        $invoicesSql = "SELECT DISTINCT i.id, i.invoice_number,
                        JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name')) as customer_name
                        FROM invoices i
                        INNER JOIN {$this->table} r ON r.invoice_id = i.id
                        ORDER BY i.invoice_number DESC";
        $stmt = $this->db->prepare($invoicesSql);
        $stmt->execute();
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'payment_methods' => $paymentMethods,
            'invoices' => $invoices
        ];
    }

    /**
     * Legacy method - Get all receipts with optional filters
     */
    public function getAll($limit = 10, $offset = 0, $status = '', $invoiceId = '')
    {
        $filters = [];
        if (!empty($invoiceId)) {
            $filters['invoice_id'] = $invoiceId;
        }
        if (!empty($status)) {
            $filters['status'] = $status;
        }
        
        return $this->getAllWithFilters($limit, $offset, $filters);
    }

    /**
     * Legacy method - Count total number of receipts with optional filters
     */
    public function count($status = '', $invoiceId = '')
    {
        $filters = [];
        if (!empty($invoiceId)) {
            $filters['invoice_id'] = $invoiceId;
        }
        if (!empty($status)) {
            $filters['status'] = $status;
        }
        
        return $this->countWithFilters($filters);
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