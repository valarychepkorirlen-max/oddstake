<?php
echo "<h2>Firestore Write Test</h2>";

require_once 'config/firebase.php';

$testId = 'test_' . time();
$testData = [
    'name' => 'Test User',
    'phone' => '254712345678',
    'status' => 'test',
    'created_at' => date('Y-m-d H:i:s')
];

echo "Attempting to write to Firestore...<br>";
echo "Document ID: $testId<br>";
echo "Data: <pre>" . print_r($testData, true) . "</pre><br>";

$result = $firebase->saveDocument('users', $testId, $testData);

if (isset($result['error'])) {
    echo "<span style='color:red'>❌ Write failed!</span><br>";
    echo "Error: " . $result['error'] . "<br>";
    if (isset($result['details'])) {
        echo "Details: <pre>" . print_r($result['details'], true) . "</pre><br>";
    }
} else {
    echo "<span style='color:green'>✅ Write successful!</span><br>";
    
    // Try to read it back
    $read = $firebase->getDocument('users', $testId);
    if ($read) {
        echo "<span style='color:green'>✅ Read successful!</span><br>";
        echo "Read data: <pre>" . print_r($read, true) . "</pre><br>";
        
        // Clean up
        $firebase->deleteDocument('users', $testId);
        echo "<span style='color:green'>✅ Test document cleaned up</span><br>";
    } else {
        echo "<span style='color:red'>❌ Could not read back the document</span><br>";
    }
}
?>