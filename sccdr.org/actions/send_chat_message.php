<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
}

$message_id = $_POST['message_id'] ?? null;
$reply_text = $_POST['reply_text'] ?? '';
$is_new_thread = isset($_POST['is_new_thread']) && $_POST['is_new_thread'] == 1;

if (empty($reply_text)) {
    die(json_encode(['status' => 'error', 'message' => 'Message content is empty']));
}

// Determine sender
$sender_id = null;
$sender_type = 'member';

if (isset($_SESSION['user_id'])) {
    $sender_id = $_SESSION['user_id'];
    $sender_type = 'member';
} elseif (isset($_SESSION['admin_id'])) {
    $sender_id = $_SESSION['admin_id'];
    $sender_type = 'admin';
} else {
    die(json_encode(['status' => 'error', 'message' => 'Unauthenticated session']));
}

try {
    if ($is_new_thread) {
        // Fetch user info for the new thread
        $stmtU = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmtU->execute([$sender_id]);
        $user = $stmtU->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die(json_encode(['status' => 'error', 'message' => 'User profile not found']));
        }

        // Create new thread in messages table
        $stmtMsg = $pdo->prepare("INSERT INTO messages (user_id, first_name, last_name, email, phone, message, status, is_read) VALUES (?, ?, ?, ?, ?, ?, 'open', 0)");
        $stmtMsg->execute([
            $user['id'],
            $user['first_name'],
            $user['last_name'],
            $user['email'],
            $user['phone'] ?? '',
            $reply_text
        ]);
        
        echo json_encode(['status' => 'success', 'message' => 'New support thread initialized', 'thread_id' => $pdo->lastInsertId()]);
    } else {
        // Append to existing thread
        if (!$message_id) {
            die(json_encode(['status' => 'error', 'message' => 'Thread ID missing']));
        }

        $stmtReply = $pdo->prepare("INSERT INTO message_replies (message_id, sender_id, sender_type, reply_text) VALUES (?, ?, ?, ?)");
        $stmtReply->execute([$message_id, $sender_id, $sender_type, $reply_text]);

        // Mark main message as 'open' again (or 'replied' if admin sent it)
        $newStatus = ($sender_type === 'admin') ? 'replied' : 'open';
        $stmtUpdate = $pdo->prepare("UPDATE messages SET status = ?, is_read = ? WHERE id = ?");
        $stmtUpdate->execute([$newStatus, ($sender_type === 'admin' ? 1 : 0), $message_id]);

        echo json_encode(['status' => 'success', 'message' => 'Message dispatched to thread']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
