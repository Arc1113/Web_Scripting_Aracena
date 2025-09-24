<?php
// Simple test file to check if API is accessible
echo "API Test - Current time: " . date('Y-m-d H:i:s');
echo "<br>";
echo "PHP Version: " . phpversion();
echo "<br>";
echo "JSON functions available: " . (function_exists('json_encode') ? 'Yes' : 'No');
echo "<br>";
echo "File write test: ";

$testFile = '../data/test.txt';
$testContent = 'Test at ' . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent) !== false) {
    echo "Success";
    unlink($testFile); // Clean up
} else {
    echo "Failed";
}
?>