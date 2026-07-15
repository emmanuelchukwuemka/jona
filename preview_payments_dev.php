<?php
// preview_payments_dev.php
// Mock setup for payments preview
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock the environment
define('ADMIN_SECTION', true);
require_once 'includes/config.php';

// Mock user for the table
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role='admin'");
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    // Seed an admin if none exists for preview
    $pdo->exec("INSERT INTO users (first_name, last_name, email, membership_category, role, password_hash) VALUES ('Admin', 'Preview', 'admin@example.com', 'Professional Member', 'admin', 'nopass')");
}

// Seed a transaction if none exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions");
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    $pdo->exec("INSERT INTO transactions (user_id, stripe_session_id, amount, status) VALUES (1, 'sess_preview123', 100.00, 'succeeded')");
}

// CSS needed for the sidebar and basic layout to see context
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" href="admin/css/style.css">
    <style>
        body { background: #f4f7fc; margin: 0; padding: 0; }
        .main-preview { padding: 40px; margin-left: 0; }
    </style>
</head>
<body>
    <div class="main-preview">
        <?php include 'admin/includes/sections/payments.php'; ?>
    </div>
</body>
</html>
