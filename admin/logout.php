<?php
require_once '../config/config.php';

// Destroy session and redirect to admin login
session_destroy();
header('Location: ' . BASE_URL . 'admin/login.php');
exit();
?>
