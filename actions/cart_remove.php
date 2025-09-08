<?php
require_once '../config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$id = isset($_GET['id']) ? (string)sanitizeInput($_GET['id']) : '';
if ($id && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

header('Location: ' . BASE_URL . 'cart.php');
exit();
?>


