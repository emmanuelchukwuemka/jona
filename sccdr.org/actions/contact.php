<?php
// actions/contact.php
// Hardened professional contact form processor
ob_start(); // Buffer output to prevent accidental text breaking JSON
header('Content-Type: application/json');

try {
    // Robust path resolution
    $configPath = dirname(__DIR__) . '/includes/config.php';
    if (!file_exists($configPath)) {
        throw new Exception("Configuration file not found.");
    }
    require_once $configPath;

    // Ensure the messages table exists and is synchronized
    $pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) DEFAULT NULL,
        `first_name` varchar(100) NOT NULL,
        `last_name` varchar(100) NOT NULL,
        `email` varchar(255) NOT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `message` text NOT NULL,
        `status` ENUM('open', 'replied', 'closed') NOT NULL DEFAULT 'open',
        `is_read` tinyint(1) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Create the message_replies table for 2-way communication
    $pdo->exec("CREATE TABLE IF NOT EXISTS `message_replies` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `message_id` int(11) NOT NULL,
        `admin_id` int(11) NOT NULL,
        `reply_text` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`message_id`) REFERENCES `messages`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Access denied: Invalid request method.");
    }

    // Input sanitization and member identification
    session_start();
    $userId    = $_SESSION['user_id'] ?? null;
    $firstName = trim($_POST['firstname'] ?? '');
    $lastName  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $message   = trim($_POST['comments'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
        throw new Exception("Operation failed: Required information is missing.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Validation failed: Incorrect email format.");
    }

    $stmt = $pdo->prepare("INSERT INTO messages (user_id, first_name, last_name, email, phone, message) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$userId, $firstName, $lastName, $email, $phone, $message])) {
        // Send email to admin (shhh, fail silently if mail server is not configured on local dev)
        try {
            $subject = "SCCDR Registry: New Inquiry from $firstName $lastName";
            $body = "Portal Alert: You have received a new professional inquiry.\n\nName: $firstName $lastName\nEmail: $email\nPhone: $phone\n\nInquiry Details:\n$message\n\n--\nAction Required: Please review the inquiry in your Admin Console.";
            $headers = "From: noreply@sccdr.org\r\nReply-To: $email\r\n";
            @mail("info@sccdr.org.ng", $subject, $body, $headers);
        } catch (Exception $mailErr) {
            // Mail fail is secondary to database success
        }

        ob_clean();
        echo json_encode(['status' => 'success', 'message' => 'Correspondence received. We will contact you shortly.']);
    } else {
        throw new Exception("Portal busy: Unable to archive message. Please try again.");
    }

} catch (Exception $e) {
    ob_clean();
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database synchronization failure.']);
}
?>

