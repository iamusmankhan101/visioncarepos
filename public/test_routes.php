<?php

// Simple route testing script
echo "<h2>Route Testing Script</h2>";
echo "<hr>";

// Test direct access to business routes
echo "<h3>Testing Business Routes:</h3>";

$routes = [
    '/business/select' => 'Business Selection Page',
    '/business/register' => 'Business Registration Page'
];

foreach ($routes as $route => $description) {
    echo "<p><strong>$description:</strong> ";
    echo "<a href='$route' target='_blank'>$route</a>";
    echo "</p>";
}

echo "<hr>";
echo "<h3>Manual Route Test:</h3>";
echo "<p>Click the links above to test if the routes work directly.</p>";

echo "<hr>";
echo "<h3>Current Request Info:</h3>";
echo "<p><strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Server Name:</strong> " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<hr>";
echo "<p><strong>Note:</strong> Delete this file after testing for security reasons.</p>";
?>