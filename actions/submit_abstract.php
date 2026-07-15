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
$file_path     = null;

// Handle File Upload
if (isset($_FILES['abstract_file']) && $_FILES['abstract_file']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['abstract_file']['tmp_name'];
    $fileName    = $_FILES['abstract_file']['name'];
    $fileSize    = $_FILES['abstract_file']['size'];
    $fileType    = $_FILES['abstract_file']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = array('pdf', 'doc', 'docx');
    if (in_array($fileExtension, $allowedfileExtensions)) {
        $uploadFileDir = '../uploads/abstracts/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        
        $newFileName = $user_id . '_' . time() . '_' . md5(time() . $fileName) . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $file_path = '/uploads/abstracts/' . $newFileName;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'There was some error moving the file to upload directory.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions)]);
        exit;
    }
}

// Validate: Title, Authors are required. Either Abstract Text or File Path must be provided.
if (!$title || !$authors || (!$abstract_text && !$file_path)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide the research title, authors, and either an abstract text or a file.']);
    exit;
}

// Ensure table exists (Updated with file_path)
$pdo->exec("CREATE TABLE IF NOT EXISTS `abstracts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `title` varchar(500) NOT NULL,
    `authors` varchar(500) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'General',
    `abstract_text` text DEFAULT NULL,
    `file_path` varchar(500) DEFAULT NULL,
    `status` ENUM('submitted','review','accepted','rejected') NOT NULL DEFAULT 'submitted',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

try {
    $stmt = $pdo->prepare("INSERT INTO `abstracts` (user_id, title, authors, category, abstract_text, file_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $authors, $category, $abstract_text ?: null, $file_path]);

    echo json_encode([
        'status'  => 'success',
        'message' => '✓ Abstract submitted successfully! Our editorial team will review it and update the status.'
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
