<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID required']);
    exit();
}

$query = "SELECT user_id, first_name, last_name, email, phone, role, blood_group, 
          date_of_birth, address, city, emergency_contact, created_at 
          FROM users WHERE user_id = $id";
$result = $conn->query($query);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}
?>