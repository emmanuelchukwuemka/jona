<?php
$files = ['update_admin.php', 'setup_admin.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "Deleted: $file<br>";
        } else {
            echo "Failed to delete: $file<br>";
        }
    } else {
        echo "File not found: $file<br>";
    }
}
// Self-destruct
unlink(__FILE__);
?>
