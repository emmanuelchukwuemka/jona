<?php
// webhook_stripe.php
require_once 'includes/config.php';
require_once 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, STRIPE_WEBHOOK_SECRET
    );
} catch (\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit;
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit;
}

// Handle the event
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    
    $userId = $session->metadata->user_id ?? null;
    $amount = ($session->amount_total / 100);
    $currency = $session->currency;
    $stripeSessionId = $session->id;
    $stripeCustomerId = $session->customer;

    if ($userId) {
        $pdo->beginTransaction();
        
        try {
            // Update user subscription status
            $expiryDate = date('Y-m-d H:i:s', strtotime('+1 year'));
            $stmt = $pdo->prepare("UPDATE users SET subscription_status = 'active', subscription_end = ?, stripe_customer_id = ? WHERE id = ?");
            $stmt->execute([$expiryDate, $stripeCustomerId, $userId]);

            // Log transaction
            $stmt2 = $pdo->prepare("INSERT INTO transactions (user_id, stripe_session_id, amount, currency, status) VALUES (?, ?, ?, ?, 'succeeded')");
            $stmt2->execute([$userId, $stripeSessionId, $amount, strtoupper($currency)]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            // Log the error
            error_log("Stripe Webhook DB Error: " . $e->getMessage());
        }
    }
}

http_response_code(200);
