<?php
// actions/delete_post.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorised.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

require_once '../includes/config.php';

$id = (int) ($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid post ID.']);
    exit;
}

try {
    // Grab image path to delete the file too
    $stmt = $pdo->prepare("SELECT featured_image FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post && $post['featured_image']) {
        $filePath = __DIR__ . '/../' . ltrim($post['featured_image'], '/');
        if (file_exists($filePath)) @unlink($filePath);
    }

    $del = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $del->execute([$id]);

    echo json_encode(['status' => 'success', 'message' => 'Post deleted.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
