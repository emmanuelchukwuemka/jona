<?php
require_once 'includes/config.php';

$adminEmail = 'admin@sccdr.org';
$adminPass = 'Admin123!';

// Check if admin exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);

if ($stmt->fetch()) {
    die("Master Admin account already exists. You can log in at membership.php!");
}

$hash = password_hash($adminPass, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, institution, membership_category, role, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($stmt->execute(['Master', 'Admin', $adminEmail, 'SCCDR Core', 'Professional Member', 'admin', $hash])) {
    echo "<h1>Admin Account Created Safely!</h1>";
    echo "<p>Email: <b>$adminEmail</b></p>";
    echo "<p>Password: <b>$adminPass</b></p>";
    echo "<p><a href='membership.php'>Click here to Log In</a></p>";
    echo "<p style='color: red;'>SECURITY WARNING: Delete this setup_admin.php file after logging in!</p>";
} else {
    echo "Failed to create admin account.";
}
?>

