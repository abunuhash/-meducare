<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Medicine ID required']);
    exit();
}

$query = "SELECT * FROM medicines WHERE medicine_id = $id";
$result = $conn->query($query);

if ($result->num_rows == 1) {
    $medicine = $result->fetch_assoc();
    echo json_encode($medicine);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Medicine not found']);
}
?>