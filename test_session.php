<?php
// Test session functionality
session_save_path('/var/www/dev.dashboard.africawatersolutions.org/writable/session');
session_name('aws_dev_session');
session_start();

echo "<h2>Session Test</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Save Path:</strong> " . session_save_path() . "</p>";

// Set a test value
$_SESSION['test'] = 'Session is working! ' . time();

echo "<p><strong>Session Data:</strong></p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<p><strong>Session Files:</strong></p>";
$files = scandir('/var/www/dev.dashboard.africawatersolutions.org/writable/session');
echo "<pre>";
print_r($files);
echo "</pre>";

echo "<p><strong>Cookies:</strong></p>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

echo "<p>Refresh this page to see if session persists.</p>";
?>
```

Save and exit, then visit:
```
https://dev.impact-outsourcing.com/test_session.php
