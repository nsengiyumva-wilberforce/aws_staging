<?php
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';

$db_config = $db['default'];
echo "Testing connection to: " . $db_config['database'] . " on " . $db_config['hostname'] . "\n";

try {
    $conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    echo "Database connected successfully\n";
    
    // Test users table
    $result = $conn->query('SHOW TABLES LIKE "users"');
    if ($result->num_rows > 0) {
        echo "Users table exists\n";
        
        // Check if there are any users
        $users = $conn->query('SELECT COUNT(*) as count FROM users');
        $count = $users->fetch_assoc();
        echo "Number of users: " . $count['count'] . "\n";
    } else {
        echo "Users table NOT found\n";
    }
    $conn->close();
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
