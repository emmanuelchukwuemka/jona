<?php
// actions/submit_abstract.php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorised. Please log in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

require_once '../includes/config.php';

$title         = trim($_POST['title'] ?? '');
$authors       = trim($_POST['authors'] ?? '');
$category      = trim($_POST['category'] ?? 'General');
$abstract_text = trim($_POST['abstract_text'] ?? '');
$user_id       = (int) $_SESSION['user_id'];

// Validate
if (!$title || !$authors || !$abstract_text) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit;
}

// Ensure table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `abstracts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `title` varchar(500) NOT NULL,
    `authors` varchar(500) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'General',
    `abstract_text` text NOT NULL,
    `status` ENUM('submitted','review','accepted','rejected') NOT NULL DEFAULT 'submitted',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

try {
    $stmt = $pdo->prepare("INSERT INTO `abstracts` (user_id, title, authors, category, abstract_text) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $authors, $category, $abstract_text]);

    echo json_encode([
        'status'  => 'success',
        'message' => '✓ Abstract submitted successfully! Our editorial team will review it and update the status.'
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again.']);
}
