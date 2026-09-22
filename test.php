<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

echo "<h2>Meducare Database Test</h2>";

// Test connection
if ($conn) {
    echo "<p style='color: green;'>✓ Database connected successfully!</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
}

// Test query
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p>✓ Users table found. Total users: " . $row['count'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ Error querying users table: " . $conn->error . "</p>";
}

// Show PHP info
phpinfo();
?>