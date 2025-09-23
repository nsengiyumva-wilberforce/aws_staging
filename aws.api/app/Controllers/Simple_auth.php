<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Simple_auth extends ResourceController
{
    public function authenticate()
    {
        header('Content-Type: application/json');
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            http_response_code(404);
            echo json_encode(['status' => 404, 'message' => 'Missing credentials']);
            return;
        }
        
        // Direct database connection
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM admin_user_view WHERE email = ? AND active = 1", [$username]);
        $user = $query->getRow();
        
        if ($user && $user->password === $password) {
            // Success
            unset($user->password);
            $response = [
                'status' => 200,
                'error' => null,
                'message' => 'Authentication successful',
                'data' => $user
            ];
            echo json_encode($response);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 404, 'message' => 'Invalid credentials']);
        }
    }
}
