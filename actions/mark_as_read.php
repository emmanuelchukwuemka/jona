<?php
// actions/mark_as_read.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

require_once dirname(__DIR__) . '/includes/config.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
    exit;
}

$stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(['status' => 'success', 'message' => 'Marked as read.']);
?>
