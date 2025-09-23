<?php
include_once('application/config/database.php');
$db_config = $db['default'];

$connection = mysqli_connect(
    $db_config['hostname'], 
    $db_config['username'], 
    $db_config['password'], 
    $db_config['database']
);

if ($connection) {
    echo "Database connection: SUCCESS\n";
    
    // Test if users table exists
    $result = mysqli_query($connection, "SHOW TABLES LIKE '%user%'");
    echo "User tables: " . mysqli_num_rows($result) . " found\n";
    
    if ($row = mysqli_fetch_array($result)) {
        echo "Table found: " . $row[0] . "\n";
        
        // Count users
        $count_result = mysqli_query($connection, "SELECT COUNT(*) as count FROM " . $row[0]);
        $count = mysqli_fetch_array($count_result);
        echo "User count: " . $count['count'] . "\n";
    }
    
    mysqli_close($connection);
} else {
    echo "Database connection: FAILED - " . mysqli_connect_error() . "\n";
}
?>
