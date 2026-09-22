<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

$term = isset($_GET['term']) ? $_GET['term'] : '';

if (strlen($term) < 2) {
    echo json_encode([]);
    exit();
}

$term = $conn->real_escape_string($term);

$query = "SELECT medicine_id, name, generic_name, category, manufacturer, price 
          FROM medicines 
          WHERE name LIKE '%$term%' 
          OR generic_name LIKE '%$term%' 
          OR manufacturer LIKE '%$term%'
          LIMIT 10";
$result = $conn->query($query);

$medicines = [];
while ($row = $result->fetch_assoc()) {
    $medicines[] = $row;
}

echo json_encode($medicines);
?>