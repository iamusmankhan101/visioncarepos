<?php
session_start();

echo "<h2>Business Session Fixer</h2>";

if (!isset($_SESSION["selected_business_id"])) {
    $_SESSION["selected_business_id"] = 1; // Default to business ID 1
    echo "<p>✅ Set selected_business_id to 1</p>";
} else {
    echo "<p>✅ Business already selected: " . $_SESSION["selected_business_id"] . "</p>";
}

echo "<p><a href='/pos/create'>Try POS create now</a></p>";
?>