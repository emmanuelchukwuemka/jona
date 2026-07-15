<?php
session_start();
header('Content-Type: application/json');

require_once '../includes/config.php';

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid email address.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, status FROM subscribers WHERE email = ?");
    $stmt->execute([$email]);
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sub) {
        if ($sub['status'] === 'active') {
            echo json_encode(['status' => 'success', 'message' => 'You are already subscribed!']);
        } else {
            $stmt2 = $pdo->prepare("UPDATE subscribers SET status = 'active' WHERE id = ?");
            $stmt2->execute([$sub['id']]);
            echo json_encode(['status' => 'success', 'message' => 'Welcome back! You have been resubscribed.']);
        }
    } else {
        $stmt3 = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
        $stmt3->execute([$email]);
        echo json_encode(['status' => 'success', 'message' => 'Thank you for subscribing!']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
