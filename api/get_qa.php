<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Question ID required']);
    exit();
}

// Update view count
$conn->query("UPDATE qa SET views = views + 1 WHERE qa_id = $id");

$query = "SELECT q.*, 
          u1.first_name as asker_first, u1.last_name as asker_last,
          u2.first_name as answerer_first, u2.last_name as answerer_last
          FROM qa q
          JOIN users u1 ON q.user_id = u1.user_id
          LEFT JOIN users u2 ON q.answered_by = u2.user_id
          WHERE q.qa_id = $id";

$result = $conn->query($query);

if ($result->num_rows == 1) {
    $qa = $result->fetch_assoc();
    echo json_encode([
        'qa_id' => $qa['qa_id'],
        'question' => $qa['question'],
        'answer' => $qa['answer'],
        'category' => $qa['category'],
        'asked_by' => $qa['asker_first'] . ' ' . $qa['asker_last'],
        'answered_by' => $qa['answerer_first'] ? $qa['answerer_first'] . ' ' . $qa['answerer_last'] : null,
        'date' => date('M d, Y', strtotime($qa['created_at'])),
        'answered_date' => $qa['answered_at'] ? date('M d, Y', strtotime($qa['answered_at'])) : null,
        'views' => $qa['views'],
        'likes' => $qa['likes']
    ]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Question not found']);
}
?>