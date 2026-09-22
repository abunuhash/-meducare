<?php
// setup-database.php - Run this once to set up database

$host = 'localhost';
$username = 'root';
$password = '';

// Create connection without database
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS meducare_db";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db('meducare_db');

// Read SQL file
$sql_file = file_get_contents('database.sql');

// Execute multi queries
if ($conn->multi_query($sql_file)) {
    echo "SQL file imported successfully<br>";
    
    // Clear results
    while ($conn->next_result()) {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    }
} else {
    echo "Error importing SQL file: " . $conn->error . "<br>";
}

// Test the database
$test = $conn->query("SELECT COUNT(*) as count FROM users");
if ($test) {
    $row = $test->fetch_assoc();
    echo "Users table created with " . $row['count'] . " records<br>";
}

$conn->close();
echo "<h2>Database setup complete!</h2>";
echo "<a href='home.php'>Go to Meducare Home</a>";
?>