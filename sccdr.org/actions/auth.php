<?php
ob_start(); // buffer stray PHP notices/warnings
session_start();

try {
    require_once '../includes/config.php';
} catch (Throwable $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed. Please try again later.']);
    exit;
}

ob_end_clean(); // discard any stray output from config
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    if ($password !== $passwordConfirm) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters.']);
        exit;
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Email address is already registered.']);
        exit;
    }

    // Handle optional profile picture upload
    $profilePicPath = null;
    if (!empty($_FILES['profile_picture']['tmp_name'])) {
        $file    = $_FILES['profile_picture'];
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2 MB

        if (!in_array($file['type'], $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'Profile picture must be JPG, PNG, GIF or WebP.']);
            exit;
        }
        if ($file['size'] > $maxSize) {
            echo json_encode(['status' => 'error', 'message' => 'Profile picture must be under 2MB.']);
            exit;
        }

        $uploadDir = __DIR__ . '/../assets/uploads/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . uniqid('', true) . '.' . strtolower($ext);
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $profilePicPath = '/assets/uploads/avatars/' . $filename;
        }
    }

    // Hash password & Insert
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, institution, membership_category, password_hash, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$firstName, $lastName, $email, $institution, $category, $hash, $profilePicPath])) {
        // Automatically log them in
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        $_SESSION['user_email'] = $email;
        $_SESSION['role'] = 'member';
        
        echo json_encode(['status' => 'success', 'message' => 'Registration successful! Redirecting...', 'redirect' => 'dashboard.php']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred during registration.']);
    }

} elseif ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter both email and password.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, first_name, last_name, password_hash, role, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        if (isset($user['status']) && $user['status'] === 'suspended') {
            echo json_encode(['status' => 'error', 'message' => 'Your account has been suspended. Please contact support.']);
            exit;
        }
        // Generate secure session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $email;
        $_SESSION['role'] = $user['role'];

        $destination = ($user['role'] === 'admin') ? 'admin/index.php' : 'dashboard.php';

        echo json_encode(['status' => 'success', 'message' => 'Login successful! Redirecting...', 'redirect' => $destination]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
}
?>

