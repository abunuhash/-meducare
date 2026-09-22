<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Please login']);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT q.*, 
          (SELECT answer FROM qa WHERE user_id = q.user_id AND question LIKE CONCAT('%', q.question, '%') LIMIT 1) as answer
          FROM user_questions q 
          WHERE q.user_id = $user_id 
          ORDER BY q.created_at DESC";
$result = $conn->query($query);

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

echo json_encode($questions);
?>