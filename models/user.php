<?php
/**
 * User Model
 * 
 * Handles all user-related database operations
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';

class User {
    private $db;
    private $table = 'users';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new user
     * 
     * @param array $data User data
     * @return int|false User ID or false on failure
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} (email, password, name, role, created_at) 
                    VALUES (:email, :password, :name, :role, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':email' => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':name' => $data['name'],
                ':role' => $data['role'] ?? ROLE_VIEWER
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("User creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by ID
     * 
     * @param int $id User ID
     * @return array|false
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User find error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by email
     * 
     * @param string $email Email address
     * @return array|false
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = :email AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User find error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all users with pagination
     * 
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getAll($limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT id, email, name, role, created_at, updated_at 
                    FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    ORDER BY created_at DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("User fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total users
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
            error_log("User count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Update user
     * 
     * @param int $id User ID
     * @param array $data User data
     * @return bool
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $params = [':id' => $id];
            
            if (isset($data['email'])) {
                $fields[] = "email = :email";
                $params[':email'] = $data['email'];
            }
            
            if (isset($data['name'])) {
                $fields[] = "name = :name";
                $params[':name'] = $data['name'];
            }
            
            if (isset($data['role'])) {
                $fields[] = "role = :role";
                $params[':role'] = $data['role'];
            }
            
            if (isset($data['password'])) {
                $fields[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $fields[] = "updated_at = NOW()";
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("User update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete user
     * 
     * @param int $id User ID
     * @return bool
     */
    public function delete($id) {
        try {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("User delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify user password
     * 
     * @param string $email Email
     * @param string $password Password
     * @return array|false User data or false
     */
    public function verifyCredentials($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get user permissions
     * 
     * @param int $userId User ID
     * @return array
     */
    public function getPermissions($userId) {
        try {
            $sql = "SELECT module, actions FROM user_permissions WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            $permissions = [];
            while ($row = $stmt->fetch()) {
                $permissions[$row['module']] = json_decode($row['actions'], true);
            }
            
            return $permissions;
        } catch (PDOException $e) {
            error_log("Permission fetch error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Set user permissions
     * 
     * @param int $userId User ID
     * @param array $permissions Permissions array
     * @return bool
     */
    public function setPermissions($userId, $permissions) {
        try {
            $this->db->beginTransaction();
            
            // Delete existing permissions
            $sql = "DELETE FROM user_permissions WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            // Insert new permissions
            $sql = "INSERT INTO user_permissions (user_id, module, actions) VALUES (:user_id, :module, :actions)";
            $stmt = $this->db->prepare($sql);
            
            foreach ($permissions as $module => $actions) {
                $stmt->execute([
                    ':user_id' => $userId,
                    ':module' => $module,
                    ':actions' => json_encode($actions)
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Permission set error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search users
     * 
     * @param string $query Search query
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function search($query, $limit = RECORDS_PER_PAGE, $offset = 0) {
        try {
            $sql = "SELECT id, email, name, role, created_at 
                    FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    AND (email LIKE :query OR name LIKE :query)
                    ORDER BY created_at DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("User search error: " . $e->getMessage());
            return [];
        }
    }
}
