<?php
echo "<h2>Firestore Connection Test</h2>";

require_once 'config/firebase.php';

// Test 1: Check if we can read from Firestore
echo "<h3>1. Testing Firestore Read:</h3>";
try {
    $tips = getLatestTips(1);
    echo "✅ Successfully read from Firestore!<br>";
    echo "Found " . count($tips) . " tips<br>";
} catch (Exception $e) {
    echo "❌ Read failed: " . $e->getMessage() . "<br>";
}

// Test 2: Try to write a test document
echo "<h3>2. Testing Firestore Write:</h3>";
try {
    $testId = 'test_' . time();
    $testData = [
        'test' => 'connection',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    $result = $firebase->saveDocument('test', $testId, $testData);
    
    if (isset($result['error'])) {
        echo "❌ Write failed: " . $result['error'] . "<br>";
        echo "Full response: <pre>" . print_r($result, true) . "</pre><br>";
    } else {
        echo "✅ Successfully wrote to Firestore!<br>";
        
        // Clean up test document
        $firebase->deleteDocument('test', $testId);
        echo "✅ Test document cleaned up<br>";
    }
} catch (Exception $e) {
    echo "❌ Write exception: " . $e->getMessage() . "<br>";
}

// Test 3: Check Firebase configuration
echo "<h3>3. Firebase Configuration:</h3>";
echo "API Key: " . substr($firebase->apiKey, 0, 10) . "...<br>";
echo "Project ID: " . $firebase->projectId . "<br>";
echo "Base URL: " . $firebase->baseUrl . "<br>";

// Test 4: Check Firestore Rules
echo "<h3>4. Firestore Rules Check:</h3>";
echo "Make sure your Firestore rules allow writes. Go to Firestore Console > Rules tab.<br>";
echo "For testing, use these rules:<br>";
echo "<pre>
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    match /{document=**} {
      allow read, write: if true;
    }
  }
}
</pre>";
?>