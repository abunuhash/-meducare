<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit();
}

// Validate required fields
$required = ['first_name', 'last_name', 'email', 'phone', 'password', 'role'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "$field is required"]);
        exit();
    }
}

$first_name = $conn->real_escape_string($data['first_name']);
$last_name = $conn->real_escape_string($data['last_name']);
$email = $conn->real_escape_string($data['email']);
$phone = $conn->real_escape_string($data['phone']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$role = $conn->real_escape_string($data['role']);

// Check if email exists
$check_query = "SELECT user_id FROM users WHERE email = '$email'";
$check_result = $conn->query($check_query);

if ($check_result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already registered']);
    exit();
}

// Insert user
$insert_query = "INSERT INTO users (first_name, last_name, email, phone, password, role) 
                VALUES ('$first_name', '$last_name', '$email', '$phone', '$password', '$role')";

if ($conn->query($insert_query)) {
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'user_id' => $conn->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed']);
}
?>