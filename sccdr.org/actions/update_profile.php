<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

$userId      = $_SESSION['user_id'];
$firstName   = trim($_POST['first_name'] ?? '');
$lastName    = trim($_POST['last_name'] ?? '');
$institution = trim($_POST['institution'] ?? '');

if (empty($firstName) || empty($lastName)) {
    echo json_encode(['status' => 'error', 'message' => 'First and last name are required.']);
    exit;
}

try {
    // Handle profile picture upload if provided
    $profilePic = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/img/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed)) {
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadDir . $fileName)) {
                $profilePic = '/assets/img/avatars/' . $fileName;
            }
        }
    }

    if ($profilePic) {
        $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, institution=?, profile_picture=? WHERE id=?");
        $stmt->execute([$firstName, $lastName, $institution, $profilePic, $userId]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, institution=? WHERE id=?");
        $stmt->execute([$firstName, $lastName, $institution, $userId]);
    }

    // Update session name just in case
    $_SESSION['user_name'] = $firstName . ' ' . $lastName;

    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
