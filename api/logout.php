<?php
require_once '../includes/config.php';

session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully',
    'redirect' => 'home.php'
]);
?>