<?php
require_once '../application/config/constants.php';
echo "API_BASE_URL: " . API_BASE_URL . "\n";
echo "Full URL: " . API_BASE_URL . 'test_auth.php' . "\n";
$url = API_BASE_URL . 'test_auth.php';
$params = [
    'email' => 'info@tunamojja.com',
    'password' => 'tunamojja.2018',
    'format' => 'json'
];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP Code: $http_code\n";
echo "Response: $response\n";
echo "JSON Decoded: " . print_r(json_decode($response), true) . "\n";
?>
