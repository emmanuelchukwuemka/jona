<?php
/**
 * Direct Database Configuration for SCCDR Platform
 */

// Production credentials (uncomment for live server)
// $dbHost = 'localhost';
// $dbUser = 'sccdrorg_sccdrorg_sccdr_db_prod';
// $dbPass = 'sccdrorg_sccdr_db_prod';
// $dbName = 'sccdrorg_sccdr_db_prod';

// Local development credentials
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'sccdr_db';

// Stripe Configuration
define('STRIPE_SECRET_KEY', 'sk_test_placeholder');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_placeholder');
define('STRIPE_WEBHOOK_SECRET', 'whsec_placeholder');

try {
    // Basic connection logic
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Re-throw with clear message for auth.php to catch
    throw new RuntimeException("Database connection failed: " . $e->getMessage());
}

// Subscription pricing mapping
$membership_prices = [
    'Student Member'        => ['amount' => 50,  'price_id' => 'price_student_id'],
    'Professional Member'   => ['amount' => 100, 'price_id' => 'price_professional_id'],
    'Institutional Member'  => ['amount' => 500, 'price_id' => 'price_institutional_id'],
    'Fellow (FSCCDR)'       => ['amount' => 200, 'price_id' => 'price_fellow_id'],
];
?>
