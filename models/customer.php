<?php
/**
 * Customer Model
 * 
 * Handles all customer-related database operations
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class Customer {
    private $db;
    private $table = 'customers';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new customer
     * 
     * @param array $data Customer data
     * @return int|false Customer ID or false on failure
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (name, email, phone, address, company, created_by, created_at) 
                    VALUES (:name, :email, :phone, :address, :company, :created_by, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'] ?? null,
                ':phone' => $data['phone'],
                ':address' => $data['address'] ?? null,
                ':company' => $data['company'] ?? null,
                ':created_by' => $data['created_by']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Customer creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find customer by ID
     * 
     * @param int $id Customer ID
     * @return array|false
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Customer find error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all customers with pagination
     * 
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT c.*, u.name as created_by_name 
                    FROM {$this->table} c
                    LEFT JOIN users u ON c.created_by = u.id
                    WHERE c.deleted_at IS NULL 
                    ORDER BY c.id ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Customer fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total customers
     * 
     * @return int
     */
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Customer count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Update customer
     * 
     * @param int $id Customer ID
     * @param array $data Customer data
     * @return bool
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE {$this->table} 
                    SET name = :name, 
                        email = :email, 
                        phone = :phone, 
                        address = :address, 
                        company = :company,
                        updated_at = NOW() 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':email' => $data['email'] ?? null,
                ':phone' => $data['phone'],
                ':address' => $data['address'] ?? null,
                ':company' => $data['company'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Customer update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete customer
     * 
     * @param int $id Customer ID
     * @return bool
     */
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Customer delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count search results
     * 
     * @param string $query Search query
     * @return int
     */
    public function countSearch($query) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM {$this->table} c
                    WHERE c.deleted_at IS NULL 
                    AND (c.name LIKE :query1 OR c.email LIKE :query2 OR c.phone LIKE :query3 OR c.company LIKE :query4)";
            
            $stmt = $this->db->prepare($sql);
            $searchTerm = "%$query%";
            $stmt->bindValue(':query1', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':query2', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':query3', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':query4', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Customer search count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Search customers
     * 
     * @param string $query Search query
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function search($query, $limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT c.*, u.name as created_by_name 
                    FROM {$this->table} c
                    LEFT JOIN users u ON c.created_by = u.id
                    WHERE c.deleted_at IS NULL 
                    AND (c.name LIKE :query1 OR c.email LIKE :query2 OR c.phone LIKE :query3 OR c.company LIKE :query4)
                    ORDER BY c.id ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $searchTerm = "%$query%";
            $stmt->bindValue(':query1', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':query2', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':query3', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':query4', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Customer search error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get customer purchase history
     * 
     * @param int $customerId Customer ID
     * @return array
     */
    public function getPurchaseHistory($customerId) {
        try {
            $sql = "SELECT s.*, c.code as coil_code, c.name as coil_name 
                    FROM sales s
                    LEFT JOIN coils c ON s.coil_id = c.id
                    WHERE s.customer_id = :customer_id 
                    ORDER BY s.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':customer_id' => $customerId]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Purchase history error: " . $e->getMessage());
            return [];
        }
    }
}
