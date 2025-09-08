<?php
/**
 * Menu Item Model
 * Handles menu item-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class MenuItem {
    private $conn;
    private $table_name = "menu_items";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all menu items
     * @return array
     */
    public function getAllMenuItems() {
        $query = "SELECT mi.*, r.name as restaurant_name 
                  FROM " . $this->table_name . " mi 
                  JOIN restaurants r ON mi.restaurant_id = r.id 
                  WHERE mi.status = 'available' AND r.status = 'active' 
                  ORDER BY mi.name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get popular menu items (first 8)
     * @return array
     */
    public function getPopularItems() {
        $query = "SELECT mi.*, r.name as restaurant_name 
                  FROM " . $this->table_name . " mi 
                  JOIN restaurants r ON mi.restaurant_id = r.id 
                  WHERE mi.status = 'available' AND r.status = 'active' 
                  ORDER BY mi.id ASC LIMIT 8";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get menu items by restaurant ID
     * @param int $restaurant_id
     * @return array
     */
    public function getMenuItemsByRestaurant($restaurant_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE restaurant_id = :restaurant_id AND status = 'available' 
                  ORDER BY category, name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':restaurant_id', $restaurant_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get menu item by ID
     * @param int $id
     * @return array|null
     */
    public function getMenuItemById($id) {
        $query = "SELECT mi.*, r.name as restaurant_name 
                  FROM " . $this->table_name . " mi 
                  JOIN restaurants r ON mi.restaurant_id = r.id 
                  WHERE mi.id = :id AND mi.status = 'available'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Add new menu item (Admin only)
     * @param array $data
     * @return array
     */
    public function addMenuItem($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = "Menu item name is required";
        }
        if (empty($data['description'])) {
            $errors[] = "Description is required";
        }
        if (empty($data['price']) || !is_numeric($data['price'])) {
            $errors[] = "Valid price is required";
        }
        if (empty($data['restaurant_id'])) {
            $errors[] = "Restaurant is required";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (restaurant_id, name, description, price, image, category) 
                  VALUES (:restaurant_id, :name, :description, :price, :image, :category)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':restaurant_id', $data['restaurant_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':category', $data['category']);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Menu item added successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to add menu item']];
        }
    }

    /**
     * Update menu item (Admin only)
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateMenuItem($id, $data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = "Menu item name is required";
        }
        if (empty($data['description'])) {
            $errors[] = "Description is required";
        }
        if (empty($data['price']) || !is_numeric($data['price'])) {
            $errors[] = "Valid price is required";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, price = :price, 
                      image = :image, category = :category 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Menu item updated successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to update menu item']];
        }
    }

    /**
     * Delete menu item (Admin only)
     * @param int $id
     * @return array
     */
    public function deleteMenuItem($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Menu item deleted successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to delete menu item']];
        }
    }

    /**
     * Search menu items
     * @param string $search
     * @return array
     */
    public function searchMenuItems($search) {
        $query = "SELECT mi.*, r.name as restaurant_name 
                  FROM " . $this->table_name . " mi 
                  JOIN restaurants r ON mi.restaurant_id = r.id 
                  WHERE mi.status = 'available' AND r.status = 'active' 
                  AND (mi.name LIKE :search OR mi.description LIKE :search) 
                  ORDER BY mi.name ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Sort menu items by price
     * @param string $order (asc or desc)
     * @return array
     */
    public function sortMenuItemsByPrice($order = 'asc') {
        $orderBy = $order === 'desc' ? 'DESC' : 'ASC';
        $query = "SELECT mi.*, r.name as restaurant_name 
                  FROM " . $this->table_name . " mi 
                  JOIN restaurants r ON mi.restaurant_id = r.id 
                  WHERE mi.status = 'available' AND r.status = 'active' 
                  ORDER BY mi.price " . $orderBy;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get menu items by category
     * @param string $category
     * @return array
     */
    public function getMenuItemsByCategory($category) {
        $query = "SELECT mi.*, r.name as restaurant_name 
                  FROM " . $this->table_name . " mi 
                  JOIN restaurants r ON mi.restaurant_id = r.id 
                  WHERE mi.status = 'available' AND r.status = 'active' 
                  AND mi.category = :category 
                  ORDER BY mi.name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all categories
     * @return array
     */
    public function getAllCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table_name . " 
                  WHERE status = 'available' AND category IS NOT NULL 
                  ORDER BY category ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
