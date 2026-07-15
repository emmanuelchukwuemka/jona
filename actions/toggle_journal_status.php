<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

require_once '../includes/config.php';

$id = (int) ($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid journal ID.']);
    exit;
}

$current = $pdo->prepare("SELECT status FROM journals WHERE id = ?");
$current->execute([$id]);
$journal = $current->fetch(PDO::FETCH_ASSOC);

if (!$journal) {
    echo json_encode(['status' => 'error', 'message' => 'Journal not found.']);
    exit;
}

$newStatus = $journal['status'] === 'published' ? 'draft' : 'published';

$stmt = $pdo->prepare("UPDATE journals SET status = ? WHERE id = ?");
$stmt->execute([$newStatus, $id]);

echo json_encode(['status' => 'success', 'new_status' => $newStatus]);
?>
