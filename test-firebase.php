<?php
echo "<h2>Firebase REST API Test</h2>";

require_once 'config/firebase.php';

// Test 1: Check if Firebase class exists
echo "<h3>1. Firebase Client Check:</h3>";
if (class_exists('FirebaseRestClient')) {
    echo "✅ FirebaseRestClient class loaded<br>";
} else {
    echo "❌ FirebaseRestClient class not found<br>";
}

// Test 2: Test connection
echo "<h3>2. Connection Test:</h3>";
try {
    $tips = getLatestTips(5);
    echo "✅ Successfully connected to Firestore!<br>";
    echo "Found " . count($tips) . " tips in database<br>";
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: PHP Configuration
echo "<h3>3. PHP Configuration:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "cURL: " . (function_exists('curl_version') ? "✅ Enabled" : "❌ Disabled") . "<br>";
echo "JSON: " . (function_exists('json_decode') ? "✅ Enabled" : "❌ Disabled") . "<br>";

// Test 4: Directory permissions
echo "<h3>4. Directory Permissions:</h3>";
echo "logs/ directory: " . (is_writable('logs/') ? "✅ Writable" : "❌ Not writable") . "<br>";
echo "config/ directory: " . (is_readable('config/') ? "✅ Readable" : "❌ Not readable") . "<br>";
?>