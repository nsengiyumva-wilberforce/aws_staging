<?php
// Test direct dashboard access
define('BASEPATH', '');
include_once('application/config/config.php');

echo "Testing dashboard access...\n";
echo "Base URL: " . $config['base_url'] . "\n";

// Try to access dashboard URL directly
$dashboard_url = $config['base_url'] . 'dashboard';
echo "Dashboard URL: " . $dashboard_url . "\n";

// Test with curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dashboard_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response length: " . strlen($response) . " chars\n";

if (strpos($response, 'login') !== false) {
    echo "RESULT: Redirects to login\n";
} elseif (strpos($response, 'dashboard') !== false) {
    echo "RESULT: Dashboard accessible\n";
} else {
    echo "RESULT: Unknown response\n";
}
?>
