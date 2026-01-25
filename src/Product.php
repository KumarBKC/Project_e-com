<?php
/**
 * Product Model Class
 * 
 * Handles all product-related database operations
 */

class Product {
    private $pdo;
    private $table = 'products';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get all products
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get product by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Search products
     */
    public function search($query) {
        $search_term = "%{$query}%";
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE name LIKE ? OR description LIKE ? ORDER BY name");
        $stmt->execute([$search_term, $search_term]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get products by category
     */
    public function getByCategory($category) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE category = ? ORDER BY name");
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all categories
     */
    public function getCategories() {
        $stmt = $this->pdo->query("SELECT DISTINCT category FROM {$this->table} ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Create new product
     */
    public function create($name, $price, $description = '', $category = '', $image = '') {
        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} (name, price, description, category, image, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        
        return $stmt->execute([$name, $price, $description, $category, $image]);
    }
    
    /**
     * Update product
     */
    public function update($id, $data) {
        $allowed = ['name', 'price', 'description', 'category', 'image'];
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
     * Delete product
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get product count
     */
    public function count() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM {$this->table}");
        return $stmt->fetch()['count'];
    }
    
    /**
     * Check if product exists
     */
    public function exists($id) {
        $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() !== false;
    }
}
?>
