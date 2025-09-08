<?php
/**
 * User Model
 * Handles user-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Register a new user
     * @param array $data
     * @return array
     */
    public function register($data) {
        $errors = [];
        
        // Validate input
        if (empty($data['username'])) {
            $errors[] = "Username is required";
        }
        if (empty($data['email'])) {
            $errors[] = "Email is required";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        if (empty($data['password'])) {
            $errors[] = "Password is required";
        } elseif (strlen($data['password']) < 6) {
            $errors[] = "Password must be at least 6 characters";
        }
        if (empty($data['full_name'])) {
            $errors[] = "Full name is required";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check if username or email already exists
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username OR email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $errors[] = "Username or email already exists";
            return ['success' => false, 'errors' => $errors];
        }

        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert user
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password, full_name, phone, address) 
                  VALUES (:username, :email, :password, :full_name, :phone, :address)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful'];
        } else {
            return ['success' => false, 'errors' => ['Registration failed']];
        }
    }

    /**
     * Login user
     * @param string $username
     * @param string $password
     * @return array
     */
    public function login($username, $password) {
        $query = "SELECT id, username, email, password, full_name FROM " . $this->table_name . " 
                  WHERE username = :username OR email = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            if (password_verify($password, $user['password'])) {
                return [
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'full_name' => $user['full_name']
                    ]
                ];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid username or password'];
    }

    /**
     * Get user by ID
     * @param int $id
     * @return array|null
     */
    public function getUserById($id) {
        $query = "SELECT id, username, email, full_name, phone, address, created_at 
                  FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Update user profile
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateProfile($id, $data) {
        $errors = [];
        
        if (empty($data['full_name'])) {
            $errors[] = "Full name is required";
        }
        if (empty($data['email'])) {
            $errors[] = "Email is required";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET full_name = :full_name, email = :email, phone = :phone, address = :address 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            return ['success' => false, 'errors' => ['Update failed']];
        }
    }
}
?>
