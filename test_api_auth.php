<?php
// Test API authentication directly
$url = 'https://dev.impact-outsourcing.com/aws.api/public/admin-user/authenticate';
$data = array(
    'username' => 'info@tunamojja.com',
    'password' => 'tunamojja.2018'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

echo "Testing authentication...\n";
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

echo "HTTP Code: $http_code\n";
echo "cURL Error: $error\n";
echo "Response: $response\n";

curl_close($ch);
?>
