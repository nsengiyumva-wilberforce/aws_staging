<?php
header('Content-Type: application/json');
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
if ($email && $password) {
    echo json_encode([
        'status' => 200,
        'error' => null,
        'message' => 'Authentication successful',
        'data' => [
            'user_id' => 1,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'region_id' => 1,
            'region_code' => 'C',
            'permission_list' => '{"dashboard":1,"users":1,"reports":1}'
        ]
    ]);
} else {
    http_response_code(404);
    echo json_encode(['status' => 404, 'message' => 'Missing credentials']);
}
?>
