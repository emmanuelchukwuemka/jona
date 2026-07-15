<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

require_once '../includes/config.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
    exit;
}

// Get the file path before deleting
$stmt = $pdo->prepare("SELECT file_path FROM journals WHERE id = ?");
$stmt->execute([$id]);
$journal = $stmt->fetch(PDO::FETCH_ASSOC);

if ($journal) {
    // Delete the physical file
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $journal['file_path'];
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
    // Remove from database
    $pdo->prepare("DELETE FROM journals WHERE id = ?")->execute([$id]);
    echo json_encode(['status' => 'success', 'message' => 'Journal deleted.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Journal not found.']);
}
?>

