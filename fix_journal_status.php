<?php
require_once 'includes/config.php';

// Add status column if missing
$cols = $pdo->query("SHOW COLUMNS FROM `journals`")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('status', $cols)) {
    $pdo->exec("ALTER TABLE `journals` ADD COLUMN `status` ENUM('published','draft') NOT NULL DEFAULT 'published' AFTER `published_date`");
    echo "✅ Added missing 'status' column.<br>";
} else {
    echo "ℹ️ 'status' column already exists.<br>";
}

// Fix any rows with NULL or empty status
$fixed = $pdo->exec("UPDATE `journals` SET `status` = 'published' WHERE `status` IS NULL OR `status` = ''");
echo "✅ Fixed $fixed journal(s) with missing status — set to 'published'.<br>";

// Show current counts
$published = $pdo->query("SELECT COUNT(*) FROM `journals` WHERE status = 'published'")->fetchColumn();
$draft     = $pdo->query("SELECT COUNT(*) FROM `journals` WHERE status = 'draft'")->fetchColumn();
echo "<br><strong>Current status counts:</strong><br>";
echo "📗 Published: $published<br>";
echo "📄 Draft: $draft<br>";
echo "<br>✅ Done. You can delete this file now.";
?>
