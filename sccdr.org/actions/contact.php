<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

// Auto-create messages table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(50) DEFAULT NULL,
    `message` text NOT NULL,
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$firstName = trim($_POST['firstname'] ?? '');
$lastName  = trim($_POST['lastname'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$message   = trim($_POST['comments'] ?? '');

if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO messages (first_name, last_name, email, phone, message) VALUES (?, ?, ?, ?, ?)");
if ($stmt->execute([$firstName, $lastName, $email, $phone, $message])) {
    echo json_encode(['status' => 'success', 'message' => 'Your message has been sent! We will get back to you shortly.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message. Please try again.']);
}
?>

