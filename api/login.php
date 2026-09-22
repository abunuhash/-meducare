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

$email = $conn->real_escape_string($data['email']);
$password = $data['password'];
$role = $conn->real_escape_string($data['role']);

$query = "SELECT * FROM users WHERE email = '$email' AND role = '$role'";
$result = $conn->query($query);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'role' => $user['role'],
            'redirect' => $user['role'] == 'admin' ? 'admin/dashboard.php' : 'patient/dashboard.php'
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid password']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}
?>