<?php
require_once '../config/config.php';

$_SESSION['cart'] = [];

header('Location: ' . BASE_URL . 'cart.php');
exit();
?>


