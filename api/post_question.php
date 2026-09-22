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

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login to ask a question']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request data']);
    exit();
}

$user_id = $_SESSION['user_id'];
$category = $conn->real_escape_string($data['category']);
$question = $conn->real_escape_string($data['question']);
$is_anonymous = isset($data['anonymous']) ? ($data['anonymous'] ? 1 : 0) : 0;

if (empty($question) || strlen($question) < 10) {
    http_response_code(400);
    echo json_encode(['error' => 'Question must be at least 10 characters long']);
    exit();
}

$query = "INSERT INTO user_questions (user_id, question, category, is_anonymous) 
          VALUES ($user_id, '$question', '$category', $is_anonymous)";

if ($conn->query($query)) {
    echo json_encode([
        'success' => true,
        'message' => 'Question submitted successfully',
        'question_id' => $conn->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to submit question']);
}
?>