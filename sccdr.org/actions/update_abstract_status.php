<?php
// actions/update_abstract_status.php
ob_start(); // buffer any stray PHP warnings/notices
session_start();

// Admin only — check before any output
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorised.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

try {
    require_once '../includes/config.php';
} catch (Throwable $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

ob_end_clean(); // discard any stray output from config.php
header('Content-Type: application/json');

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
