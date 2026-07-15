<?php
// fix_db.php
// Script to ensure database schema is up-to-date
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

    // 2. Ensure message_replies exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `message_replies` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `message_id` int(11) NOT NULL,
        `sender_id` int(11) NOT NULL,
        `sender_type` ENUM('admin', 'member') NOT NULL DEFAULT 'admin',
        `reply_text` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`message_id`) REFERENCES `messages`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Ensured message_replies exists.\n";

    // 3. Update abstracts table for file uploads
    $abstractCols = $pdo->query("DESCRIBE abstracts")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('file_path', $abstractCols)) {
        $pdo->exec("ALTER TABLE abstracts ADD COLUMN file_path VARCHAR(500) DEFAULT NULL AFTER abstract_text");
        echo "Added file_path to abstracts.\n";
    }
    
    // Ensure abstract_text is nullable
    $pdo->exec("ALTER TABLE abstracts MODIFY COLUMN abstract_text TEXT DEFAULT NULL");
    echo "Updated abstract_text to be nullable.\n";

    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
