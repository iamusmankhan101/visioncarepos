<?php
session_start();

echo "<h2>POS Access Debug</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";

echo "<h3>Business Selection Check:</h3>";
if (isset($_SESSION["selected_business_id"])) {
    echo "<p>✅ Selected Business ID: " . $_SESSION["selected_business_id"] . "</p>";
} else {
    echo "<p>❌ No business selected in session</p>";
    echo "<p><a href='/business/select'>Select a business first</a></p>";
}

echo "<h3>Session Data:</h3>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3>Next Steps:</h3>";
echo "<ul>";
if (!isset($_SESSION["selected_business_id"])) {
    echo "<li><a href='/business/select'>Select a business</a></li>";
}
echo "<li><a href='/pos/create'>Try POS create again</a></li>";
echo "</ul>";
?>