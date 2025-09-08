<?php
require_once '../config/config.php';
require_once '../includes/Order.php';

requireLogin();

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit();
}

$order_id = (int)$_GET['id'];
$order = new Order();

// Get order details
$order_details = $order->getOrderById($order_id);

if (!$order_details) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

// Check if the order belongs to the current user
if ($order_details['user_id'] != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Get order items
$order_items = $order->getOrderItems($order_id);

echo json_encode([
    'success' => true,
    'order' => $order_details,
    'items' => $order_items
]);
?>
