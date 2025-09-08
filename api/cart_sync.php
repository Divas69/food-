<?php
require_once '../config/config.php';

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data) || !isset($data['items']) || !is_array($data['items'])) {
    echo json_encode(['success' => false]);
    exit();
}

$_SESSION['cart_items'] = $data['items'];
echo json_encode(['success' => true]);
?>


