<?php
// actions/create_post.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorised.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

require_once '../includes/config.php';

$title    = trim($_POST['title']    ?? '');
$excerpt  = trim($_POST['excerpt']  ?? '');
$content  = trim($_POST['content']  ?? '');
$category = trim($_POST['category'] ?? 'News');
$author   = trim($_POST['author']   ?? 'SCCDR Admin');
$status   = in_array($_POST['status'] ?? '', ['published','draft']) ? $_POST['status'] : 'published';

if (!$title || !$content) {
    echo json_encode(['status' => 'error', 'message' => 'Title and content are required.']);
    exit;
}

// Generate slug
$slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
$slug = trim($slug, '-');
$slug = substr($slug, 0, 80);
// Ensure uniqueness
$baseSlug = $slug;
$i = 1;
while (true) {
    $check = $pdo->prepare("SELECT id FROM posts WHERE slug = ?");
    $check->execute([$slug]);
    if (!$check->fetch()) break;
    $slug = $baseSlug . '-' . $i++;
}

// Handle image upload
$imagePath = null;
if (!empty($_FILES['featured_image']['tmp_name'])) {
    $file    = $_FILES['featured_image'];
    $allowed = ['image/jpeg','image/png','image/webp'];
    $maxSize = 3 * 1024 * 1024;

    if (!in_array($file['type'], $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Image must be JPG, PNG or WebP.']);
        exit;
    }
    if ($file['size'] > $maxSize) {
        echo json_encode(['status' => 'error', 'message' => 'Image must be under 3MB.']);
        exit;
    }

    $uploadDir = __DIR__ . '/../assets/uploads/posts/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'post_' . uniqid('', true) . '.' . strtolower($ext);
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        $imagePath = '/assets/uploads/posts/' . $filename;
    }
}

// Ensure posts table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `posts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(500) NOT NULL,
    `slug` varchar(520) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'News',
    `excerpt` varchar(500) DEFAULT NULL,
    `content` longtext NOT NULL,
    `featured_image` varchar(500) DEFAULT NULL,
    `status` ENUM('published','draft') NOT NULL DEFAULT 'published',
    `author` varchar(200) NOT NULL DEFAULT 'SCCDR Admin',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

try {
    $stmt = $pdo->prepare("
        INSERT INTO posts (title, slug, category, excerpt, content, featured_image, status, author)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $slug, $category, $excerpt ?: null, $content, $imagePath, $status, $author]);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Post published.',
        'slug'    => $slug,
        'id'      => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
