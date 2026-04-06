<?php
// includes/config.php
// Configuration and Database Connection

$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = ''; // Default XAMPP/WAMP empty password
$dbName = 'sccdr_db';

try {
    // Connect to MySQL server without selecting a database first
    $pdo = new PDO("mysql:host=$dbHost;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `$dbName`;");

    // Create 'users' table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `first_name` varchar(100) NOT NULL,
            `last_name` varchar(100) NOT NULL,
            `email` varchar(255) NOT NULL,
            `institution` varchar(255) DEFAULT NULL,
            `membership_category` varchar(100) NOT NULL,
            `role` ENUM('member', 'admin') NOT NULL DEFAULT 'member',
            `password_hash` varchar(255) NOT NULL,
            `profile_picture` varchar(500) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Stripe Configuration (Test Keys - Replace with Live in production)
    define('STRIPE_SECRET_KEY', 'sk_test_51P...placeholder');
    define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51P...placeholder');
    define('STRIPE_WEBHOOK_SECRET', 'whsec_...placeholder');

    // Subscription pricing mapping
    $membership_prices = [
        'Student Member'        => ['amount' => 50,  'price_id' => 'price_student_id'],
        'Professional Member'   => ['amount' => 100, 'price_id' => 'price_professional_id'],
        'Institutional Member'  => ['amount' => 500, 'price_id' => 'price_institutional_id'],
        'Fellow (FSCCDR)'       => ['amount' => 200, 'price_id' => 'price_fellow_id'],
    ];

    // Migrations — add columns that older installs may not have
    try {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `profile_picture` varchar(500) DEFAULT NULL;");
        $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `status` ENUM('active','suspended') NOT NULL DEFAULT 'active';");
        $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `subscription_status` ENUM('inactive', 'active', 'expired') NOT NULL DEFAULT 'inactive';");
        $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `subscription_end` DATETIME DEFAULT NULL;");
        $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `stripe_customer_id` VARCHAR(255) DEFAULT NULL;");
    } catch (PDOException $e) { /* Columns may already exist */ }

    // Transactions table
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `transactions` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `stripe_session_id` varchar(255) NOT NULL,
                `amount` decimal(10,2) NOT NULL,
                `currency` varchar(10) NOT NULL DEFAULT 'USD',
                `status` varchar(50) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    } catch (PDOException $e) { /* Table may already exist */ }

    // Password reset tokens table
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `password_resets` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `email` varchar(255) NOT NULL,
                `code` varchar(6) NOT NULL,
                `expires_at` datetime NOT NULL,
                `used` tinyint(1) NOT NULL DEFAULT 0,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    } catch (PDOException $e) { /* Table may already exist */ }

    // Journals migrations
    try {
        $pdo->exec("ALTER TABLE `journals` ADD COLUMN IF NOT EXISTS `cover_image` varchar(500) DEFAULT NULL;");
    } catch (PDOException $e) { /* Table or column may already exist, or table doesn't exist yet */ }

} catch (PDOException $e) {
    throw new RuntimeException("Database connection failed: " . $e->getMessage(), 0, $e);
}
?>
