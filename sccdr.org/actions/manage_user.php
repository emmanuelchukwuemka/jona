<?php
session_start();
header('Content-Type: application/json');

// Only admins may call this endpoint
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$userId = (int)($_POST['user_id'] ?? $_GET['user_id'] ?? 0);

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

switch ($action) {

    // ── GET FULL USER DETAILS ─────────────────────────────────────────────────
    case 'get_details':
        $stmt = $pdo->prepare("
            SELECT id, first_name, last_name, email, institution,
                   membership_category, role, status, profile_picture, created_at
            FROM users WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        // Count abstracts for this user
        $abstractCount = 0;
        try {
            $s = $pdo->prepare("SELECT COUNT(*) FROM abstracts WHERE user_id = ?");
            $s->execute([$userId]);
            $abstractCount = (int)$s->fetchColumn();
        } catch (PDOException $e) {}

        $user['abstract_count'] = $abstractCount;
        $user['created_at_fmt'] = date('d M Y, g:i A', strtotime($user['created_at']));

        echo json_encode(['success' => true, 'user' => $user]);
        break;

    // ── SUSPEND USER ──────────────────────────────────────────────────────────
    case 'suspend':
        // Prevent admin from suspending themselves
        if ($userId === (int)$_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'You cannot suspend your own account.']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE users SET status = 'suspended' WHERE id = ? AND role != 'admin'");
        $stmt->execute([$userId]);
        if ($stmt->rowCount()) {
            echo json_encode(['success' => true, 'message' => 'User has been suspended.', 'status' => 'suspended']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not suspend user (admin accounts cannot be suspended).']);
        }
        break;

    // ── UNSUSPEND USER ────────────────────────────────────────────────────────
    case 'unsuspend':
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->execute([$userId]);
        echo json_encode(['success' => true, 'message' => 'User account has been reactivated.', 'status' => 'active']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}
