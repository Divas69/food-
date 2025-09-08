<?php
require_once '../config/config.php';

// Destroy session and redirect to home
session_destroy();
header('Location: ' . BASE_URL . 'index.php');
exit();
?>
