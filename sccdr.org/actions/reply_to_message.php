<?php
// actions/reply_to_message.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';

$messageId = (int)($_POST['message_id'] ?? 0);
$replyText = trim($_POST['reply_text'] ?? '');
$adminId   = $_SESSION['admin_id'] ?? 1; // Fallback to 1 if not set

if ($messageId <= 0 || empty($replyText)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Save the reply
    $stmt = $pdo->prepare("INSERT INTO message_replies (message_id, admin_id, reply_text) VALUES (?, ?, ?)");
    $stmt->execute([$messageId, $adminId, $replyText]);

    // 2. Update parent message status
    $stmt2 = $pdo->prepare("UPDATE messages SET status = 'replied' WHERE id = ?");
    $stmt2->execute([$messageId]);

    // 3. Fetch user details for email
    $stmt3 = $pdo->prepare("SELECT first_name, email FROM messages WHERE id = ?");
    $stmt3->execute([$messageId]);
    $msg = $stmt3->fetch(PDO::FETCH_ASSOC);

    $pdo->commit();

    // 4. Send Email Notification
    if ($msg && !empty($msg['email'])) {
        $to = $msg['email'];
        $subject = "SCCDR Support: Official Response to Your Inquiry";
        
        $emailContent = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
            <div style='background: #081e0f; padding: 30px; text-align: center;'>
                <img src='https://sccdr.org/assets/img/logo.png' alt='SCCDR Logo' style='width: 150px;'>
            </div>
            <div style='padding: 30px; border: 1px solid #e2e8f0;'>
                <h2 style='color: #081e0f;'>Hello " . htmlspecialchars($msg['first_name']) . ",</h2>
                <p>The SCCDR Administration has responded to your recent inquiry.</p>
                
                <div style='background: #f8fafc; padding: 20px; border-left: 4px solid #7AD03A; margin: 20px 0;'>
                    <p style='font-style: italic; margin-bottom: 5px; color: #64748b; font-size: 12px;'>Official Response:</p>
                    <p style='margin: 0; line-height: 1.6;'>" . nl2br(htmlspecialchars($replyText)) . "</p>
                </div>

                <p>You can view your full conversation history and track your inquiries anytime via your <a href='https://sccdr.org/dashboard.php' style='color: #7AD03A; text-decoration: none; font-weight: bold;'>Member Portal</a>.</p>
                
                <p style='margin-top: 30px; border-top: 1px solid #eee; pt: 20px;'>
                    Best regards,<br>
                    <strong>SCCDR Registry Team</strong>
                </p>
            </div>
            <div style='text-align: center; font-size: 11px; color: #94a3b8; padding-top: 20px;'>
                &copy; " . date('Y') . " Journal of Community & Communication Development Research (SCCDR). All rights reserved.
            </div>
        </div>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SCCDR Administration <noreply@sccdr.org>" . "\r\n";

        @mail($to, $subject, $emailContent, $headers);
    }

    echo json_encode(['status' => 'success', 'message' => 'Reply sent successfully.']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
