<?php
/**
 * Database Configuration File for Pharmacy Project
 * Configure database connection for XAMPP
 */

class Database {
    private $host = "localhost";
    private $db_name = "pharmacy";
    private $username = "root";
    private $password = ""; // XAMPP default password is empty
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            $this->conn->set_charset("utf8mb4");
            
            if ($this->conn->connect_error) {
                throw new Exception("Conexiune esuata: " . $this->conn->connect_error);
            }
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Eroare conexiune: ' . $e->getMessage()]);
            exit();
        }
        
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
