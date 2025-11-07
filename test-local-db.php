<?php
// Test local database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'arraihan_dev';

echo "Testing local database connection...\n";

try {
    $mysqli = new mysqli($host, $user, $pass);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✓ Connected to MySQL successfully\n";
    
    // Create database if not exists
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    if ($mysqli->error) {
        echo "Error creating database: " . $mysqli->error . "\n";
    } else {
        echo "✓ Database '$dbname' ready\n";
    }
    
    // Select database
    $mysqli->select_db($dbname);
    
    // Check existing tables
    $result = $mysqli->query("SHOW TABLES");
    echo "Current tables in $dbname:\n";
    while ($row = $result->fetch_array()) {
        echo "  - " . $row[0] . "\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>