<?php
// actions/forgot_password.php
// Handles: send OTP code | verify code | reset password

ob_start();
session_start();

try {
    require_once '../includes/config.php';
} catch (Throwable $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

ob_end_clean();
header('Content-Type: application/json');

$step = $_POST['step'] ?? '';

// ════════════════════════════════════════════════════════════════════════
// STEP 1 — Request OTP: validate email, generate 6-digit code, send mail
// ════════════════════════════════════════════════════════════════════════
if ($step === 'request') {
    $email = trim(strtolower($_POST['email'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
        exit;
    }

    // Check the email exists
    $stmt = $pdo->prepare("SELECT id, first_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Always return success to prevent email enumeration
    if (!$user) {
        echo json_encode(['status' => 'success', 'message' => 'If that email is registered, a reset code has been sent.']);
        exit;
    }

    // Rate limit: max 3 requests in 15 minutes
    $rateStmt = $pdo->prepare("SELECT COUNT(*) FROM password_resets WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $rateStmt->execute([$email]);
    if ((int)$rateStmt->fetchColumn() >= 3) {
        echo json_encode(['status' => 'error', 'message' => 'Too many requests. Please wait 15 minutes before trying again.']);
        exit;
    }

    // Invalidate any previous unused codes
    $pdo->prepare("UPDATE password_resets SET used = 1 WHERE email = ? AND used = 0")->execute([$email]);

    // Generate 6-digit numeric code
    $code    = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $ins = $pdo->prepare("INSERT INTO password_resets (email, code, expires_at) VALUES (?, ?, ?)");
    $ins->execute([$email, $code, $expires]);

    // Send email
    $name    = htmlspecialchars($user['first_name']);
    $subject = "SCCDR — Your Password Reset Code";
    $body    = "Dear $name,\n\n"
             . "You requested a password reset for your SCCDR account.\n\n"
             . "Your 6-digit reset code is:\n\n"
             . "    $code\n\n"
             . "This code is valid for 15 minutes. Do not share it with anyone.\n\n"
             . "If you did not request this, please ignore this email — your account is safe.\n\n"
             . "— The SCCDR Team\n"
             . "https://sccdr.org";

    $headers  = "From: noreply@sccdr.org\r\n";
    $headers .= "Reply-To: noreply@sccdr.org\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    $sent = mail($email, $subject, $body, $headers);

    // Also store email in session so step 2 can pre-fill it
    $_SESSION['pw_reset_email'] = $email;

    if ($sent) {
        echo json_encode(['status' => 'success', 'message' => 'A 6-digit reset code has been sent to your email.']);
    } else {
        // On local dev mail() may not work — surface the code for testing
        $devNote = (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']))
                 ? " [DEV: code is <strong>$code</strong>]"
                 : '';
        echo json_encode(['status' => 'success', 'message' => 'Reset code generated.' . $devNote]);
    }
    exit;
}

// ════════════════════════════════════════════════════════════════════════
// STEP 2 — Verify code + set new password
// ════════════════════════════════════════════════════════════════════════
if ($step === 'reset') {
    $email    = trim(strtolower($_POST['email'] ?? ''));
    $code     = trim($_POST['code'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (empty($email) || empty($code) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters.']);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    // Find a valid, unused, non-expired code
    $stmt = $pdo->prepare("
        SELECT id FROM password_resets
        WHERE email = ? AND code = ? AND used = 0 AND expires_at > NOW()
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([$email, $code]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired reset code. Please request a new one.']);
        exit;
    }

    // Update password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $upd  = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $upd->execute([$hash, $email]);

    // Mark token as used
    $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?")->execute([$reset['id']]);

    // Clear session hint
    unset($_SESSION['pw_reset_email']);

    echo json_encode(['status' => 'success', 'message' => 'Password reset successfully! Redirecting to login…', 'redirect' => '/membership.php']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown step.']);
