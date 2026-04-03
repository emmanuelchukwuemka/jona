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
    $createUsersTable = "
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
    ";
    $pdo->exec($createUsersTable);

    // Add profile_picture column to existing tables that may not have it
    try {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `profile_picture` varchar(500) DEFAULT NULL;");
    } catch (PDOException $e) { /* Column may already exist */ }

} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
?>

