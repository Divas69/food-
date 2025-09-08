<?php
require_once '../config/config.php';

// Ensure session cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'menu.php');
    exit();
}

// Sanitize
$id = isset($_POST['id']) ? (string)sanitizeInput($_POST['id']) : '';
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
$price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
$restaurant_id = isset($_POST['restaurant_id']) ? (string)sanitizeInput($_POST['restaurant_id']) : '';
$restaurant_name = isset($_POST['restaurant_name']) ? sanitizeInput($_POST['restaurant_name']) : '';
$return = BASE_URL . 'cart.php';

if ($id && $name && $price > 0) {
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => 0,
            'restaurant_id' => $restaurant_id,
            'restaurant_name' => $restaurant_name,
        ];
    }
    $_SESSION['cart'][$id]['quantity'] += 1;
    setFlash($name . ' added to cart', 'success');
}

header('Location: ' . $return);
exit();
?>


