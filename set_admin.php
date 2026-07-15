<?php
/**
 * One-time script to set the secure admin password
 */
require_once 'includes/config.php';

$email = 'admin@sccdr.org.ng';
$password = 'SCCDR_Secure_Admin_#2026_!Complex_Access!_8492';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, role, password_hash, membership_category, status) 
                           VALUES ('System', 'Administrator', ?, 'admin', ?, 'Professional Member', 'active')
                           ON DUPLICATE KEY UPDATE password_hash = ?, role = 'admin'");
    
    if ($stmt->execute([$email, $hash, $hash])) {
        echo "<div style='font-family:sans-serif; padding:40px; text-align:center;'>";
        echo "<h2 style='color:#7AD03A;'>Success!</h2>";
        echo "<p>The admin password for <strong>$email</strong> has been securely set.</p>";
        echo "<p>This script will now attempt to delete itself for security.</p>";
        echo "</div>";
    }
} catch (PDOException $e) {
    die("Error setting admin password: " . $e->getMessage());
}

// Self-destruct
unlink(__FILE__);
?>
