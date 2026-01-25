<?php
/**
 * Database Connection Class
 * 
 * Handles all database connections using PDO.
 * Provides a singleton instance to avoid multiple connections.
 */

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $this->connect();
    }

    /**
     * Get singleton instance of Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish database connection
     */
    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ]);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /**
     * Get PDO connection instance
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Prepare and execute a query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /**
     * Fetch all results
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch single result
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * Error handling
     */
    private function handleError(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        
        // Show generic error to user
        if ($_ENV['APP_ENV'] ?? 'production' === 'development') {
            die("❌ Database Error: " . $e->getMessage());
        } else {
            die("❌ A database error occurred. Please try again later.");
        }
    }
}

// Create global PDO instance for backward compatibility
$pdo = Database::getInstance()->getConnection();

?>
