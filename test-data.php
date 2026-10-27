<?php
require_once 'config/firebase.php';

echo "<h2>Firestore Data Test</h2>";

// Get all tips
$tips = getLatestTips(10);

echo "<h3>Tips in Database:</h3>";
if (count($tips) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Match</th><th>League</th><th>Tip</th><th>Odds</th><th>Premium</th><th>Result</th></tr>";
    
    foreach ($tips as $tip) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($tip['match'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($tip['league'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($tip['tip'] ?? 'N/A') . "</td>";
        echo "<td>" . ($tip['tip_odds'] ?? 'N/A') . "</td>";
        echo "<td>" . (($tip['is_premium'] ?? false) ? '🔒 Premium' : '✅ Free') . "</td>";
        echo "<td>" . htmlspecialchars($tip['result'] ?? 'pending') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No tips found. Please add tips in Firestore console.</p>";
}

// Show raw data for debugging
echo "<h3>Raw Data:</h3>";
echo "<pre>";
print_r($tips);
echo "</pre>";
?>