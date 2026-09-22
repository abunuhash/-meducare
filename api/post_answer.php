<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Only admins can answer questions']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit();
}

$qa_id = intval($data['qa_id']);
$answer = $conn->real_escape_string($data['answer']);
$answered_by = $_SESSION['user_id'];

if (empty($answer)) {
    http_response_code(400);
    echo json_encode(['error' => 'Answer cannot be empty']);
    exit();
}

$query = "UPDATE qa SET answer = '$answer', answered_by = $answered_by, 
          status = 'answered', answered_at = NOW() WHERE qa_id = $qa_id";

if ($conn->query($query)) {
    echo json_encode([
        'success' => true,
        'message' => 'Answer posted successfully'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to post answer']);
}
?>