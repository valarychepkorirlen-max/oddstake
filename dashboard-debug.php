<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
require_once 'config/firebase.php';

echo "<h2>Debug Dashboard</h2>";

// Check session
echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color:red'>❌ No user_id in session. Redirecting to login...</p>";
    // header('Location: login.php');
    // exit;
} else {
    $userId = $_SESSION['user_id'];
    echo "<p>✅ User ID: $userId</p>";
    
    // Try to get user data
    echo "<h3>Fetching User Data:</h3>";
    try {
        $userData = getUserFromFirestore($userId);
        if ($userData) {
            echo "<pre>";
            print_r($userData);
            echo "</pre>";
        } else {
            echo "<p style='color:red'>❌ User data not found in Firestore</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
    
    // Test subscription check
    echo "<h3>Subscription Check:</h3>";
    try {
        $hasActive = hasActiveSubscription($userData ?? []);
        echo "<p>Has active subscription: " . ($hasActive ? 'Yes' : 'No') . "</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>Error checking subscription: " . $e->getMessage() . "</p>";
    }
    
    // Test getting payments
    echo "<h3>Getting Payments:</h3>";
    try {
        $payments = getUserPayments($userId);
        echo "<p>Found " . count($payments) . " payments</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>Error getting payments: " . $e->getMessage() . "</p>";
    }
}
?>