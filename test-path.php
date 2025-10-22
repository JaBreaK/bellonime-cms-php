<?php
echo "<h1>Path Debugging</h1>";
echo "<p>Current working directory: " . getcwd() . "</p>";
echo "<p>File __DIR__: " . __DIR__ . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<h2>Testing paths:</h2>";

// Test path to core/functions.php
$path1 = __DIR__ . '/core/functions.php';
echo "<p>__DIR__ . '/core/functions.php': " . $path1 . " - ";
echo file_exists($path1) ? "EXISTS" : "NOT FOUND";
echo "</p>";

// Test path from admin perspective
$path2 = __DIR__ . '/admin/../core/functions.php';
echo "<p>__DIR__ . '/admin/../core/functions.php': " . $path2 . " - ";
echo file_exists($path2) ? "EXISTS" : "NOT FOUND";
echo "</p>";

// Test relative path
$path3 = 'core/functions.php';
echo "<p>'core/functions.php': " . $path3 . " - ";
echo file_exists($path3) ? "EXISTS" : "NOT FOUND";
echo "</p>";

// List files in core directory
echo "<h2>Files in core directory:</h2>";
$files = scandir('core');
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "<p>$file</p>";
    }
}

// List files in admin directory
echo "<h2>Files in admin directory:</h2>";
$files = scandir('admin');
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "<p>$file</p>";
    }
}
?>