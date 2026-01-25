<?php
/**
 * User Model Class
 * 
 * Handles all user-related database operations
 */

class User {
    private $pdo;
    private $table = 'users';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT id, username, email, role, created_at FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get user by username
     */
    public function getByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * Get all users (admin only)
     */
    public function getAll() {
        $stmt = $this->pdo->query("SELECT id, username, email, role, created_at FROM {$this->table} ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Create new user
     */
    public function create($username, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (username, email, password, role) VALUES (?, ?, ?, 'user')");
        return $stmt->execute([$username, $email, $hashed_password]);
    }
    
    /**
     * Authenticate user
     */
    public function authenticate($username, $password) {
        $user = $this->getByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
        }
        
        return null;
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        $allowed = ['email', 'username'];
        $updates = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $updates[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
}
?>
