<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

require_once '../includes/config.php';

$id       = (int) ($_POST['id'] ?? 0);
$title    = trim($_POST['title'] ?? '');
$authors  = trim($_POST['authors'] ?? '');
$category = trim($_POST['category'] ?? 'Uncategorized');
$abstract = trim($_POST['abstract'] ?? '');
$keywords = trim($_POST['keywords'] ?? '');
$doi            = trim($_POST['doi'] ?? '');
$volume         = trim($_POST['volume'] ?? '');
$issue          = trim($_POST['issue'] ?? '');
$year           = trim($_POST['year'] ?? '');
$published_date = trim($_POST['published_date'] ?? '') ?: null;

if (!$id || empty($title)) {
    echo json_encode(['status' => 'error', 'message' => 'Journal ID and title are required.']);
    exit;
}

// Fetch existing record
$existing = $pdo->prepare("SELECT * FROM journals WHERE id = ?");
$existing->execute([$id]);
$journal = $existing->fetch(PDO::FETCH_ASSOC);

if (!$journal) {
    echo json_encode(['status' => 'error', 'message' => 'Journal not found.']);
    exit;
}

$newFilePath  = $journal['file_path'];
$newCoverPath = $journal['cover_image'];

// Replace PDF/DOCX if a new one was uploaded
if (!empty($_FILES['journal_file']['tmp_name'])) {
    $file    = $_FILES['journal_file'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'docx'];

    if (!in_array($ext, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Only PDF and DOCX files are allowed.']);
        exit;
    }

    $uploadDir  = dirname(__DIR__) . '/assets/pdf/';
    $fileName   = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed. Check directory permissions.']);
        exit;
    }

    // Delete old file
    $oldFile = dirname(__DIR__) . $journal['file_path'];
    if (file_exists($oldFile)) @unlink($oldFile);

    $newFilePath = '/assets/pdf/' . $fileName;
}

// Replace cover image if a new one was uploaded
if (!empty($_FILES['cover_image']['tmp_name'])) {
    $imgFile    = $_FILES['cover_image'];
    $imgExt     = strtolower(pathinfo($imgFile['name'], PATHINFO_EXTENSION));
    $imgAllowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($imgExt, $imgAllowed)) {
        $imgDir = dirname(__DIR__) . '/assets/img/journals/';
        if (!is_dir($imgDir)) mkdir($imgDir, 0755, true);

        $imgName   = 'cover_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $imgFile['name']);
        $imgTarget = $imgDir . $imgName;

        if (move_uploaded_file($imgFile['tmp_name'], $imgTarget)) {
            // Delete old cover
            if ($journal['cover_image']) {
                $oldCover = dirname(__DIR__) . $journal['cover_image'];
                if (file_exists($oldCover)) @unlink($oldCover);
            }
            $newCoverPath = '/assets/img/journals/' . $imgName;
        }
    }
}

$stmt = $pdo->prepare("UPDATE journals SET title=?, authors=?, category=?, abstract=?, keywords=?, doi=?, volume=?, issue=?, year=?, published_date=?, file_path=?, cover_image=? WHERE id=?");
if ($stmt->execute([$title, $authors, $category, $abstract, $keywords, $doi, $volume, $issue, $year, $published_date, $newFilePath, $newCoverPath, $id])) {
    echo json_encode(['status' => 'success', 'message' => 'Journal updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error. Could not update journal.']);
}
?>
