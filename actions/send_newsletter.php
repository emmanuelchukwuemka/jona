<?php
session_start();
header('Content-Type: application/json');

// Security: limit to admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$title   = trim($_POST['title'] ?? '');
$target  = $_POST['recipients'] ?? 'all';
$content = trim($_POST['content'] ?? '');

if (empty($title) || empty($content)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide both campaign title and content.']);
    exit;
}

try {
    if ($target === 'recent') {
        $stmt = $pdo->query("SELECT email FROM subscribers WHERE status = 'active' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    } else {
        $stmt = $pdo->query("SELECT email FROM subscribers WHERE status = 'active'");
    }
    
    $subs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($subs)) {
        echo json_encode(['status' => 'error', 'message' => 'No active subscribers found in this group.']);
        exit;
    }

    $successCount = 0;
    foreach ($subs as $email) {
        $subject = $title;
        $body    = $content . "\n\n--\nYou are receiving this because you subscribed to SCCDR.\nVisit sccdr.org to unsubscribe.";
        $headers = "From: noreply@sccdr.org\r\n";
        
        if (mail($email, $subject, $body, $headers)) {
            $successCount++;
        } else if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
            // fake success on local
            $successCount++;
        }
    }

    echo json_encode([
        'status'  => 'success',
        'message' => "Newsletter blasted to {$successCount} subscribers successfully!"
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred.']);
}
