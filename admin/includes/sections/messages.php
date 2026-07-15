<?php
// admin/includes/sections/messages.php
require_once __DIR__ . '/../../../includes/config.php';

// Ensure the messages table exists and is synchronized
$pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(50) DEFAULT NULL,
    `message` text NOT NULL,
    `status` ENUM('open', 'replied', 'closed') NOT NULL DEFAULT 'open',
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Ensure the message_replies table exists and is multi-sender compatible
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

// Migration: If admin_id exists, convert it to sender_id and set sender_type to 'admin'
try {
    $pdo->exec("ALTER TABLE `message_replies` CHANGE `admin_id` `sender_id` int(11) NOT NULL");
    $pdo->exec("ALTER TABLE `message_replies` ADD `sender_type` ENUM('admin', 'member') NOT NULL DEFAULT 'admin' AFTER `sender_id` ");
} catch(Exception $e) { /* Already migrated or silent fail */ }

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$unreadCount = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
?>

<!-- Contact Messages Inbox Section -->
<div id="section-messages" class="admin-section fade-in" style="display:none;">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h2 class="h2-brand" style="font-size: 32px; font-weight: 800; letter-spacing: -1px; color: #1e293b;">Correspondence Center</h2>
            <p class="p-brand" style="font-size: 15px; color: #64748b; font-weight: 400;">Integrated registry intelligence and research-member communication hub.</p>
        </div>
        <div class="col-md-5 text-end">
            <div class="badge-status-premium" style="background: #f0fdf4; border: 1px solid #dcfce7; padding: 10px 20px; border-radius: 50px;">
                <span class="pulse-emerald" style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                <span style="color: #166534; font-weight: 700; font-size: 13px;"><?= $unreadCount ?> New Inquiry<?= $unreadCount != 1 ? 'ies' : '' ?></span>
            </div>
        </div>
    </div>

    <?php if (empty($messages)): ?>
        <div class="registry-empty-state text-center py-5">
            <div class="stats-icon mx-auto mb-3" style="width:80px; height:80px; font-size:32px;">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <h4>Inbox is Current</h4>
            <p>New inquiries will appear here for administrative review.</p>
        </div>
    <?php else: ?>
        <div class="row g-4" style="height: 750px;">
            <!-- Message List Sidebar -->
            <div class="col-lg-4 h-100">
                <div class="message-sidebar-list h-100 shadow-sm" style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; overflow-y: auto;">
                    <?php foreach($messages as $i => $msg): ?>
                        <div class="message-card-item <?php echo !$msg['is_read'] ? 'is-unread' : ''; ?>" 
                             id="msg-tab-<?php echo $i; ?>"
                             onclick="viewCorrespondence(<?php echo $i; ?>, <?php echo $msg['id']; ?>)">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="msg-sender-name"><?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']); ?></span>
                                    <?php if($msg['user_id']): ?>
                                        <span class="member-tag">Member</span>
                                    <?php endif; ?>
                                </div>
                                <?php if(!$msg['is_read']): ?>
                                    <span class="unread-dot-indicator"></span>
                                <?php endif; ?>
                            </div>
                            <div class="msg-snippet-text"><?php echo htmlspecialchars(substr($msg['message'], 0, 70)) . '...'; ?></div>
                            <div class="msg-footer-tabs mt-3 d-flex justify-content-between align-items-center">
                                <span class="msg-timestamp">
                                    <i class="far fa-calendar-alt me-1"></i> <?php echo date('d M, Y', strtotime($msg['created_at'])); ?>
                                </span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="status-indicator-mini status-<?= $msg['status'] ?>"><?= ucfirst($msg['status']) ?></span>
                                    <button class="btn-card-action" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;" onclick="event.stopPropagation(); viewCorrespondence(<?php echo $i; ?>, <?php echo $msg['id']; ?>, true)">
                                        Open & Reply <i class="fas fa-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Reading and Reply Pane -->
            <div class="col-lg-8 h-100">
                <div class="message-reading-pane h-100" style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column;">
                    <?php foreach($messages as $i => $msg): ?>
                        <?php 
                            $stmtR = $pdo->prepare("SELECT * FROM message_replies WHERE message_id = ? ORDER BY created_at ASC");
                            $stmtR->execute([$msg['id']]);
                            $replies = $stmtR->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div id="msg-detail-<?php echo $i; ?>" class="message-detail-view" style="display: <?php echo $i === 0 ? 'flex' : 'none'; ?>; flex-direction: column; height: 100%; padding: 30px;">
                            <!-- Header Area -->
                            <div class="detail-header mb-4 pb-3 border-bottom d-flex justify-content-between align-items-start">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="stats-icon" style="width:52px; height:52px; font-size:20px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; border-radius: 14px; color: #22c55e; font-weight: 800; border: 1px solid #dcfce7;">
                                        <?= strtoupper(substr($msg['first_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h3 class="mb-0" style="font-weight: 800; color: #1e293b; font-size: 20px;"><?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']); ?></h3>
                                        <div style="font-size: 13px; color: #64748b; margin-top: 2px;">
                                            <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($msg['email']); ?>
                                            <?php if($msg['phone']): ?>
                                                <span class="ms-3"><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($msg['phone']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn-emerald-outline" onclick="scrollToReply(<?php echo $msg['id']; ?>)" style="padding: 10px 18px; font-size: 13px; font-weight: 700; border-radius: 12px; border: 1.5px solid #e2e8f0; background: white; transition: all 0.2s;">
                                        <i class="fas fa-reply me-2"></i> Reply
                                    </button>
                                    <button class="btn-emerald-outline-danger" style="width:42px; height:42px; border: 1.5px solid #fee2e2; color:#ef4444; padding: 0; border-radius: 12px; background: white; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onclick="trashCorrespondence(<?php echo $msg['id']; ?>, this)" title="Delete Inquiry">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Scrollable Conversation Flow -->
                            <div class="conversation-thread-v2 flex-grow-1 overflow-auto pe-3 mb-4" style="display: flex; flex-direction: column; gap: 15px;">
                                <!-- Member Inquiry Bubble -->
                                <div class="thread-bubble-member" style="align-self: flex-start; max-width: 85%; background: #f8fafc; border: 1px solid #e2e8f0; padding: 18px; border-radius: 14px; border-bottom-left-radius: 4px;">
                                    <div style="font-size: 9px; font-weight: 800; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px;">Inquiry Sent</div>
                                    <div style="font-size: 14.5px; line-height: 1.6; color: #334155;">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                    </div>
                                    <div style="font-size: 10px; color: #94a3b8; margin-top: 8px; text-align: right;"><?php echo date('d M, g:i A', strtotime($msg['created_at'])); ?></div>
                                </div>

                                <!-- Threaded Conversation History (Admin & Member) -->
                                <?php foreach($replies as $reply): ?>
                                    <div class="thread-bubble-<?= $reply['sender_type'] ?>" style="align-self: <?= $reply['sender_type'] === 'admin' ? 'flex-end' : 'flex-start' ?>; max-width: 85%; background: <?= $reply['sender_type'] === 'admin' ? '#081e0f' : '#f8fafc' ?>; color: <?= $reply['sender_type'] === 'admin' ? 'white' : '#334155' ?>; padding: 18px; border-radius: 14px; border-<?= $reply['sender_type'] === 'admin' ? 'bottom-right' : 'bottom-left' ?>-radius: 4px; border: <?= $reply['sender_type'] === 'admin' ? 'none' : '1px solid #e2e8f0' ?>; box-shadow: <?= $reply['sender_type'] === 'admin' ? '0 4px 12px rgba(8,30,15,0.1)' : 'none' ?>;">
                                        <div style="font-size: 9px; font-weight: 800; text-transform: uppercase; color: <?= $reply['sender_type'] === 'admin' ? '#7AD03A' : '#94a3b8' ?>; margin-bottom: 8px;">
                                            <?= $reply['sender_type'] === 'admin' ? 'Your Response' : 'Member Follow-up' ?>
                                        </div>
                                        <div style="font-size: 14.5px; line-height: 1.6;">
                                            <?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?>
                                        </div>
                                        <div style="font-size: 10px; color: <?= $reply['sender_type'] === 'admin' ? 'rgba(255,255,255,0.5)' : '#94a3b8' ?>; margin-top: 8px; text-align: right;"><?php echo date('d M, g:i A', strtotime($reply['created_at'])); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Quick Reply Footer -->
                            <div class="reply-editor-v2 border-top pt-4">
                                <div id="reply-alert-<?= $msg['id'] ?>" class="alert alert-mini d-none"></div>
                                <div style="position: relative;">
                                    <textarea id="reply-text-<?= $msg['id'] ?>" class="form-control-inbox" placeholder="Compose your professional response to <?= htmlspecialchars($msg['first_name']) ?>..."></textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <div style="font-size: 12px; color: #64748b; font-weight: 500;"><i class="fas fa-shield-check text-emerald me-1"></i> Official encrypted response via SCCDR Registry.</div>
                                        <button class="btn-emerald-solid" style="padding: 14px 32px; letter-spacing: 0.3px;" onclick="sendProfessionalReply(<?php echo $msg['id']; ?>, <?= $i ?>)">
                                            Dispatch Response <i class="fas fa-paper-plane ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script>
        async function viewCorrespondence(index, id, shouldScroll = false) {
            document.querySelectorAll('.message-detail-view').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.message-card-item').forEach(el => el.classList.remove('active-msg'));
            
            const detailView = document.getElementById('msg-detail-' + index);
            if(detailView) {
                detailView.style.display = 'flex';
                if(shouldScroll) {
                    setTimeout(() => scrollToReply(id), 100);
                }
            }
            
            const tabItem = document.getElementById('msg-tab-' + index);
            if(tabItem) {
                tabItem.classList.add('active-msg');
                
                if(tabItem.classList.contains('is-unread')) {
                    const formData = new FormData();
                    formData.append('id', id);
                    try {
                        const res = await fetch('../actions/mark_as_read.php', { method: 'POST', body: formData });
                        const data = await res.json();
                        if(data.status === 'success') {
                            tabItem.classList.remove('is-unread');
                            const dot = tabItem.querySelector('.unread-dot-indicator');
                            if(dot) dot.remove();
                        }
                    } catch(e) { console.error('Sync failed', e); }
                }
            }
        }

        function scrollToReply(messageId) {
            const area = document.getElementById('reply-text-' + messageId);
            if(area) {
                area.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => area.focus(), 600);
            }
        }

        async function sendProfessionalReply(messageId, index) {
            const textarea = document.getElementById('reply-text-' + messageId);
            const replyText = textarea.value.trim();
            const alertBox = document.getElementById('reply-alert-' + messageId);
            
            if(!replyText) {
                textarea.style.borderColor = '#ef4444';
                setTimeout(() => textarea.style.borderColor = '', 2000);
                return;
            }

            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i> Sending...';

            const formData = new FormData();
            formData.append('message_id', messageId);
            formData.append('reply_text', replyText);

            try {
                const res = await fetch('../actions/reply_to_message.php', { method: 'POST', body: formData });
                const data = await res.json();

                if(data.status === 'success') {
                    textarea.value = '';
                    alertBox.className = 'alert alert-mini alert-success mb-3';
                    alertBox.innerHTML = '<i class="fas fa-check-circle me-2"></i> Response dispatched successfully.';
                    alertBox.classList.remove('d-none');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.message);
                }
            } catch(e) {
                alertBox.className = 'alert alert-mini alert-danger mb-4';
                alertBox.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Delivery failed. Check connection or registry status.';
                alertBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        async function trashCorrespondence(id, btn) {
            if(!confirm('Archive this correspondence permanently?')) return;
            const formData = new FormData();
            formData.append('id', id);
            try {
                const res = await fetch('../actions/delete_message.php', { method: 'POST', body: formData });
                const data = await res.json();
                if(data.status === 'success') { location.reload(); }
            } catch(e) { alert('Archiving failed.'); }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const firstTab = document.getElementById('msg-tab-0');
            if(firstTab) firstTab.classList.add('active-msg');
        });
        </script>
    <?php endif; ?>
</div>

<style>
/* Correspondence Hub Layout Utilities */
.message-sidebar-list { overflow-x: hidden; }
.message-card-item { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: all 0.2s ease; border-left: 4px solid transparent; }
.message-card-item:hover { background: #fcfdfe; }
.message-card-item.active-msg { background: rgba(122, 208, 58, 0.04); border-left: 4px solid var(--primary-color); }
.message-card-item.is-unread { background: #f8fafc; font-weight: 600; }
.msg-sender-name { font-size: 14px; font-weight: 700; color: #1e293b; }
.member-tag { font-size: 8px; font-weight: 800; text-transform: uppercase; background: #e0f2fe; color: #0369a1; padding: 2px 7px; border-radius: 4px; }
.unread-dot-indicator { width: 8px; height: 8px; background: var(--primary-color); border-radius: 50%; box-shadow: 0 0 0 3px rgba(122, 208, 58, 0.1); }
.msg-snippet-text { font-size: 12.5px; color: #64748B; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.msg-timestamp { font-size: 11px; color: #94A3B8; font-weight: 500; }
.status-indicator-mini { font-size: 8px; font-weight: 800; text-transform: uppercase; padding: 2px 8px; border-radius: 20px; }
.status-open { background: #fef3c7; color: #92400e; }
.status-replied { background: #d1fae5; color: #065f46; }

.btn-card-action {
    background: rgba(122, 208, 58, 0.1);
    color: var(--primary-color);
    border: none;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s;
    cursor: pointer;
}
.btn-card-action:hover {
    background: var(--primary-color);
    color: white;
}
</style>
