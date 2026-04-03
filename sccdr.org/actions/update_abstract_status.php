<?php
// actions/update_abstract_status.php
session_start();
header('Content-Type: application/json');

// Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorised.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

require_once '../includes/config.php';

$id        = (int) ($_POST['id'] ?? 0);
$newStatus = trim($_POST['status'] ?? '');
$allowed   = ['submitted', 'review', 'accepted', 'rejected'];

if (!$id || !in_array($newStatus, $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE abstracts SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $id]);

    echo json_encode(['status' => 'success', 'message' => 'Status updated.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
