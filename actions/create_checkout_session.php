<?php
// actions/create_checkout_session.php
session_start();
require_once '../includes/config.php';
require_once '../vendor/autoload.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

$category = $user['membership_category'];
$pricing = $membership_prices[$category] ?? null;

if (!$pricing) {
    echo json_encode(['error' => 'Invalid membership category for payment']);
    exit;
}

try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'customer_email' => $user['email'],
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'SCCDR ' . $category . ' Annual Membership',
                ],
                'unit_amount' => $pricing['amount'] * 100, // Stripe expects cents
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/dashboard.php?payment=success',
        'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/dashboard.php?payment=cancel',
        'client_reference_id' => $user['id'],
        'metadata' => [
            'user_id' => $user['id'],
            'category' => $category
        ]
    ]);

    echo json_encode(['url' => $checkout_session->url]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Stripe Error: ' . $e->getMessage()]);
}
