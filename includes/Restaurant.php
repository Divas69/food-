<?php
/**
 * Restaurant Model
 * Handles restaurant-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class Restaurant {
    private $conn;
    private $table_name = "restaurants";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all restaurants
     * @return array
     */
    public function getAllRestaurants() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active' ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get featured restaurants (first 6)
     * @return array
     */
    public function getFeaturedRestaurants() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active' ORDER BY id ASC LIMIT 6";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get restaurant by ID
     * @param int $id
     * @return array|null
     */
    public function getRestaurantById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Add new restaurant (Admin only)
     * @param array $data
     * @return array
     */
    public function addRestaurant($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = "Restaurant name is required";
        }
        if (empty($data['description'])) {
            $errors[] = "Description is required";
        }
        if (empty($data['address'])) {
            $errors[] = "Address is required";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (name, description, address, phone, image) 
                  VALUES (:name, :description, :address, :phone, :image)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':image', $data['image']);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Restaurant added successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to add restaurant']];
        }
    }

    /**
     * Update restaurant (Admin only)
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateRestaurant($id, $data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = "Restaurant name is required";
        }
        if (empty($data['description'])) {
            $errors[] = "Description is required";
        }
        if (empty($data['address'])) {
            $errors[] = "Address is required";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, address = :address, 
                      phone = :phone, image = :image 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Restaurant updated successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to update restaurant']];
        }
    }

    /**
     * Delete restaurant (Admin only)
     * @param int $id
     * @return array
     */
    public function deleteRestaurant($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Restaurant deleted successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to delete restaurant']];
        }
    }

    /**
     * Search restaurants
     * @param string $search
     * @return array
     */
    public function searchRestaurants($search) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE status = 'active' AND (name LIKE :search OR description LIKE :search) 
                  ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
