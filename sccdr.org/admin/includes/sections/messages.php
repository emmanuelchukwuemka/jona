<?php
// Ensure the messages table exists (in case someone visits admin before any contact submission)
require_once __DIR__ . '/../../../includes/config.php';
$pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(50) DEFAULT NULL,
    `message` text NOT NULL,
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$unreadCount = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();

// Mark all as read once admin opens this panel
$pdo->exec("UPDATE messages SET is_read = 1");
?>

<!-- Contact Messages Inbox Section -->
<div id="section-messages" class="admin-section" style="display:none;">
    <div class="section-header">
        <h3>Inbox: Contact Inquiries</h3>
        <div class="header-actions">
            <span class="badge" style="background: rgba(122, 208, 58, 0.1); color: var(--primary-color);">
                <?php echo $unreadCount; ?> New <?php echo $unreadCount == 1 ? 'Message' : 'Messages'; ?>
            </span>
        </div>
    </div>

    <?php if (empty($messages)): ?>
        <div class="section-card" style="text-align: center; padding: 60px; color: #94a3b8;">
            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
            <p style="font-size: 18px; font-weight: 600;">No messages yet.</p>
            <p>When someone submits the Contact Us form, their message will appear here.</p>
        </div>
    <?php else: ?>
        <div class="message-list">
            <?php foreach($messages as $i => $msg): ?>
                <div class="message-item <?php echo !$msg['is_read'] ? 'unread' : ''; ?>" 
                     onclick="showMessage(<?php echo $i; ?>)" style="cursor:pointer;">
                    <div class="message-sender"><?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']); ?></div>
                    <div class="message-snippet"><?php echo htmlspecialchars(substr($msg['message'], 0, 90)) . '...'; ?></div>
                    <div class="message-date" style="font-size:12px; color:#94A3B8;">
                        <?php echo date('d M Y, g:i A', strtotime($msg['created_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php foreach($messages as $i => $msg): ?>
        <!-- Message Detail #<?php echo $i; ?> -->
        <div id="msg-detail-<?php echo $i; ?>" class="section-card" style="margin-top: 30px; display: <?php echo $i === 0 ? 'block' : 'none'; ?>;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 15px;">
                <div>
                    <h4 style="margin: 0; color: var(--heading-color);"><?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']); ?></h4>
                    <p style="font-size: 13px; color: #64748B; margin: 5px 0;">
                        <?php echo htmlspecialchars($msg['email']); ?>
                        <?php if($msg['phone']): ?> • <?php echo htmlspecialchars($msg['phone']); ?><?php endif; ?>
                    </p>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 12px; color: #94A3B8;">Received: <?php echo date('F d, Y', strtotime($msg['created_at'])); ?></span>
                </div>
            </div>
            <div class="message-body" style="font-size: 14px; line-height: 1.8; color: var(--heading-color); white-space: pre-wrap;"><?php echo htmlspecialchars($msg['message']); ?></div>
            <div class="message-footer" style="margin-top: 25px; display: flex; gap: 10px;">
                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="btn-upload"><i class="fas fa-reply"></i> Reply Now</a>
                <button class="btn-icon" style="padding: 10px 20px; color: #EF4444; border-color: #EF4444;" onclick="deleteMessage(<?php echo $msg['id']; ?>, this)">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </div>
        </div>
        <?php endforeach; ?>

        <script>
        function showMessage(index) {
            document.querySelectorAll('[id^="msg-detail-"]').forEach(el => el.style.display = 'none');
            document.getElementById('msg-detail-' + index).style.display = 'block';
        }
        async function deleteMessage(id, btn) {
            if(!confirm('Delete this message permanently?')) return;
            const formData = new FormData();
            formData.append('id', id);
            const res = await fetch('/actions/delete_message.php', { method: 'POST', body: formData });
            const data = await res.json();
            if(data.status === 'success') { location.reload(); }
        }
        </script>
    <?php endif; ?>
</div>

