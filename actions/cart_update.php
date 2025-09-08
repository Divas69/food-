<?php
require_once '../config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'cart.php');
    exit();
}

foreach ($_POST['qty'] ?? [] as $id => $qty) {
    $id = (string)sanitizeInput($id);
    $qty = (int)$qty;
    if ($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } else if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] = $qty;
    }
}

header('Location: ' . BASE_URL . 'cart.php');
exit();
?>


