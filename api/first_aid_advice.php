<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

$symptom = isset($_GET['symptom']) ? $_GET['symptom'] : '';

if (!$symptom) {
    echo json_encode([]);
    exit();
}

$symptom = $conn->real_escape_string($symptom);

$query = "SELECT * FROM first_aid 
          WHERE condition_name LIKE '%$symptom%' 
          OR symptoms LIKE '%$symptom%' 
          OR category LIKE '%$symptom%'
          ORDER BY 
            CASE severity
                WHEN 'Emergency' THEN 1
                WHEN 'Severe' THEN 2
                WHEN 'Moderate' THEN 3
                WHEN 'Mild' THEN 4
                ELSE 5
            END";

$result = $conn->query($query);
$advice = [];

while ($row = $result->fetch_assoc()) {
    $advice[] = [
        'condition_name' => $row['condition_name'],
        'symptoms' => $row['symptoms'],
        'first_aid_steps' => $row['first_aid_steps'],
        'warning_signs' => $row['warning_signs'],
        'severity' => $row['severity']
    ];
}

echo json_encode($advice);
?>