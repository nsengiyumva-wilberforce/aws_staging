<?php
// Simple login debug
include_once('application/config/database.php');
$db = $db['default'];

echo "Testing database connection...\n";
$conn = mysqli_connect($db['hostname'], $db['username'], $db['password'], $db['database']);

if (!$conn) {
    echo "DATABASE CONNECTION FAILED: " . mysqli_connect_error() . "\n";
    exit;
}

echo "Database connected successfully\n";

// Find user table
$tables = mysqli_query($conn, "SHOW TABLES LIKE '%user%'");
while ($table = mysqli_fetch_array($tables)) {
    echo "Found user table: " . $table[0] . "\n";
    
    // Show table structure
    $structure = mysqli_query($conn, "DESCRIBE " . $table[0]);
    echo "Table structure:\n";
    while ($field = mysqli_fetch_array($structure)) {
        echo "  " . $field['Field'] . " (" . $field['Type'] . ")\n";
    }
    
    // Count users
    $count = mysqli_query($conn, "SELECT COUNT(*) as count FROM " . $table[0]);
    $result = mysqli_fetch_array($count);
    echo "Total users: " . $result['count'] . "\n\n";
}

mysqli_close($conn);
?>
