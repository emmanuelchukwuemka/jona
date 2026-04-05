<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

require_once '../includes/config.php';

// Auto-create journals table
$pdo->exec("CREATE TABLE IF NOT EXISTS `journals` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(500) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'Uncategorized',
    `abstract` text DEFAULT NULL,
    `file_path` varchar(500) NOT NULL,
    `cover_image` varchar(500) DEFAULT NULL,
    `uploaded_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$title    = trim($_POST['title'] ?? '');
$category = trim($_POST['category'] ?? 'Uncategorized');
$abstract = trim($_POST['abstract'] ?? '');

if (empty($title)) {
    echo json_encode(['status' => 'error', 'message' => 'Journal title is required.']);
    exit;
}

if (empty($_FILES['journal_file']['tmp_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please upload a PDF or DOCX file.']);
    exit;
}

$file     = $_FILES['journal_file'];
$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed  = ['pdf', 'docx'];

if (!in_array($ext, $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Only PDF and DOCX files are allowed.']);
    exit;
}

// Save to /assets/pdf/
$uploadDir  = dirname(__DIR__) . '/assets/pdf/';
$fileName   = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
$targetPath = $uploadDir . $fileName;
$publicPath = '/assets/pdf/' . $fileName;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed. Check directory permissions.']);
    exit;
}

// Cover image logic
$publicCoverPath = null;
if (!empty($_FILES['cover_image']['tmp_name'])) {
    $imgFile = $_FILES['cover_image'];
    $imgExt  = strtolower(pathinfo($imgFile['name'], PATHINFO_EXTENSION));
    $imgAllowed = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (in_array($imgExt, $imgAllowed)) {
        $imgDir = dirname(__DIR__) . '/assets/img/journals/';
        if (!is_dir($imgDir)) mkdir($imgDir, 0755, true);
        
        $imgName   = 'cover_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $imgFile['name']);
        $imgTarget = $imgDir . $imgName;
        if (move_uploaded_file($imgFile['tmp_name'], $imgTarget)) {
            $publicCoverPath = '/assets/img/journals/' . $imgName;
        }
    }
}

$stmt = $pdo->prepare("INSERT INTO journals (title, category, abstract, file_path, cover_image, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt->execute([$title, $category, $abstract, $publicPath, $publicCoverPath, $_SESSION['user_id']])) {
    echo json_encode(['status' => 'success', 'message' => 'Journal published successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error. Could not save journal.']);
}
?>

