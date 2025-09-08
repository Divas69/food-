<?php
/**
 * Order Model
 * Handles order-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class Order {
    private $conn;
    private $table_name = "orders";
    private $order_items_table = "order_items";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create new order
     * @param array $data
     * @return array
     */
    public function createOrder($data) {
        $errors = [];
        
        if (empty($data['user_id'])) {
            $errors[] = "User ID is required";
        }
        if (empty($data['restaurant_id'])) {
            $errors[] = "Restaurant ID is required";
        }
        if (empty($data['total_amount']) || !is_numeric($data['total_amount'])) {
            $errors[] = "Valid total amount is required";
        }
        if (empty($data['items']) || !is_array($data['items'])) {
            $errors[] = "Order items are required";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $this->conn->beginTransaction();

            // Insert order
            $query = "INSERT INTO " . $this->table_name . " 
                      (user_id, restaurant_id, total_amount, delivery_address, phone, notes) 
                      VALUES (:user_id, :restaurant_id, :total_amount, :delivery_address, :phone, :notes)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':restaurant_id', $data['restaurant_id']);
            $stmt->bindParam(':total_amount', $data['total_amount']);
            $stmt->bindParam(':delivery_address', $data['delivery_address']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':notes', $data['notes']);

            if (!$stmt->execute()) {
                throw new Exception('Failed to create order');
            }

            $order_id = $this->conn->lastInsertId();

            // Insert order items
            $query = "INSERT INTO " . $this->order_items_table . " 
                      (order_id, menu_item_id, quantity, price) 
                      VALUES (:order_id, :menu_item_id, :quantity, :price)";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($data['items'] as $item) {
                $stmt->bindParam(':order_id', $order_id);
                $stmt->bindParam(':menu_item_id', $item['id']);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to add order items');
                }
            }

            $this->conn->commit();
            return ['success' => true, 'order_id' => $order_id, 'message' => 'Order created successfully'];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    /**
     * Get orders by user ID
     * @param int $user_id
     * @return array
     */
    public function getOrdersByUser($user_id) {
        $query = "SELECT o.*, r.name as restaurant_name 
                  FROM " . $this->table_name . " o 
                  JOIN restaurants r ON o.restaurant_id = r.id 
                  WHERE o.user_id = :user_id 
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get order by ID
     * @param int $order_id
     * @return array|null
     */
    public function getOrderById($order_id) {
        $query = "SELECT o.*, r.name as restaurant_name, u.full_name as user_name 
                  FROM " . $this->table_name . " o 
                  JOIN restaurants r ON o.restaurant_id = r.id 
                  JOIN users u ON o.user_id = u.id 
                  WHERE o.id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get order items by order ID
     * @param int $order_id
     * @return array
     */
    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, mi.name as item_name 
                  FROM " . $this->order_items_table . " oi 
                  JOIN menu_items mi ON oi.menu_item_id = mi.id 
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all orders (Admin only)
     * @return array
     */
    public function getAllOrders() {
        $query = "SELECT o.*, r.name as restaurant_name, u.full_name as user_name 
                  FROM " . $this->table_name . " o 
                  JOIN restaurants r ON o.restaurant_id = r.id 
                  JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update order status (Admin only)
     * @param int $order_id
     * @param string $status
     * @return array
     */
    public function updateOrderStatus($order_id, $status) {
        $valid_statuses = ['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled'];
        
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'errors' => ['Invalid status']];
        }

        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':order_id', $order_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Order status updated successfully'];
        } else {
            return ['success' => false, 'errors' => ['Failed to update order status']];
        }
    }

    /**
     * Get order statistics (Admin only)
     * @return array
     */
    public function getOrderStats() {
        $stats = [];

        // Total orders
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_orders'] = $stmt->fetch()['total'];

        // Total revenue
        $query = "SELECT SUM(total_amount) as total FROM " . $this->table_name . " WHERE status != 'cancelled'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_revenue'] = $stmt->fetch()['total'] ?: 0;

        // Orders by status
        $query = "SELECT status, COUNT(*) as count FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $status_counts = $stmt->fetchAll();
        
        $stats['status_counts'] = [];
        foreach ($status_counts as $status) {
            $stats['status_counts'][$status['status']] = $status['count'];
        }

        return $stats;
    }
}
?>
