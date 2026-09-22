<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM medicines WHERE 1=1";

if ($category && $category != 'All') {
    $query .= " AND category = '" . $conn->real_escape_string($category) . "'";
}

if ($search) {
    $search_term = $conn->real_escape_string($search);
    $query .= " AND (name LIKE '%$search_term%' OR generic_name LIKE '%$search_term%' OR manufacturer LIKE '%$search_term%')";
}

$query .= " ORDER BY name ASC";
$result = $conn->query($query);

$medicines = [];
while ($row = $result->fetch_assoc()) {
    $medicines[] = $row;
}

echo json_encode($medicines);
?>