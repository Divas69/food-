<?php
/**
 * Admin Model
 * Handles admin-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class Admin {
    private $conn;
    private $table_name = "admins";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Login admin
     * @param string $username
     * @param string $password
     * @return array
     */
    public function login($username, $password) {
        $query = "SELECT id, username, email, password FROM " . $this->table_name . " 
                  WHERE username = :username OR email = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $admin = $stmt->fetch();
            if (password_verify($password, $admin['password'])) {
                return [
                    'success' => true,
                    'admin' => [
                        'id' => $admin['id'],
                        'username' => $admin['username'],
                        'email' => $admin['email']
                    ]
                ];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid username or password'];
    }

    /**
     * Get admin by ID
     * @param int $id
     * @return array|null
     */
    public function getAdminById($id) {
        $query = "SELECT id, username, email, created_at FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }
}
?>
