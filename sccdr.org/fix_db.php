<?php
// fix_db.php
require_once 'includes/config.php';

try {
    // 1. Check if user_id exists in messages
    $columns = $pdo->query("DESCRIBE messages")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('user_id', $columns)) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN user_id INT(11) DEFAULT NULL AFTER id");
        echo "Added user_id to messages.\n";
    }

    if (!in_array('status', $columns)) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN status ENUM('open', 'replied', 'closed') NOT NULL DEFAULT 'open' AFTER message");
        echo "Added status to messages.\n";
    }

    // 2. Ensure message_replies exists (this should already work due to CREATE TABLE IF NOT EXISTS)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `message_replies` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `message_id` int(11) NOT NULL,
        `admin_id` int(11) NOT NULL,
        `reply_text` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`message_id`) REFERENCES `messages`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Ensured message_replies exists.\n";

    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
