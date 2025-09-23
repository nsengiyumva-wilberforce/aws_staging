<?php
header('Content-Type: application/json');

$username = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(404);
    echo json_encode(['status' => 404, 'message' => 'Missing credentials']);
    exit;
}

// Database connection
$mysqli = new mysqli('localhost', 'root', 'H3aven@22', 'aws_v2');

if ($mysqli->connect_error) {
    http_response_code(404);
    echo json_encode(['status' => 404, 'message' => 'Database connection failed']);
    exit;
}

$stmt = $mysqli->prepare("SELECT * FROM admin_user_view WHERE email = ? AND active = 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_object();

if ($user && $user->password === $password) {
    unset($user->password);
    echo json_encode([
        'status' => 200,
        'error' => null, 
        'message' => 'Authentication successful',
        'data' => $user
    ]);
} else {
    http_response_code(404);
    echo json_encode(['status' => 404, 'message' => 'Invalid credentials']);
}

$mysqli->close();
?>
