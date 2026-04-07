<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    echo "Attempting to include config.php...<br>";
    include 'includes/config.php';
    echo "Successfully included config.php!<br>";
    if (isset($pdo)) {
        echo "PDO object created successfully.<br>";
    } else {
        echo "PDO object is NOT defined.<br>";
    }
} catch (Throwable $e) {
    echo "Caught an error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
