<?php
require_once __DIR__ . '/../../../includes/config.php';

// Ensure subscribers table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `subscribers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `status` ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$subscribers    = $pdo->query("SELECT * FROM subscribers WHERE status = 'active' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$totalSubs      = count($subscribers);
$thisWeek       = count(array_filter($subscribers, fn($s) => strtotime($s['created_at']) >= strtotime('-7 days')));
?>

<!-- Newsletter & Subscribers -->
<div id="section-newsletters" class="admin-section" style="display:none;">
    <div class="section-header">
        <h3>Newsletter &amp; Subscribers</h3>
    </div>

    <!-- Stats -->
    <div class="subscriber-stats">
        <div class="stat-item">
            <h4 style="margin:0; color:var(--primary-color);"><?= $totalSubs ?></h4>
            <p style="font-size:13px; color:#64748b;">Total Subscribers</p>
        </div>
        <div class="stat-item">
            <h4 style="margin:0; color:#3498db;"><?= $thisWeek ?></h4>
            <p style="font-size:13px; color:#64748b;">New This Week</p>
        </div>
        <div class="stat-item">
            <h4 style="margin:0; color:#94a3b8; font-size:15px;">—</h4>
            <p style="font-size:13px; color:#64748b;">Open Rate (N/A)</p>
        </div>
    </div>

    <!-- Subscriber Table -->
    <div class="section-card">
        <div class="section-header">
            <h4>Active Subscribers</h4>
            <?php if($totalSubs > 0): ?>
            <a href="/actions/export_subscribers.php"
               style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border:1px solid var(--border-color); border-radius:8px; font-size:13px; font-weight:600; color:#64748b; text-decoration:none; background:#fff;">
                <i class="fas fa-download"></i> Export CSV
            </a>
            <?php endif; ?>
        </div>

        <?php if(empty($subscribers)): ?>
        <div style="text-align:center; padding:50px 20px; color:#94a3b8;">
            <i class="fas fa-envelope-open" style="font-size:40px; display:block; margin-bottom:14px; opacity:0.3;"></i>
            <h4 style="color:#cbd5e1; font-weight:700; margin-bottom:8px;">No subscribers yet</h4>
            <p style="font-size:13px;">Subscribers who sign up via the site will appear here.</p>
        </div>
        <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Email Address</th>
                    <th>Status</th>
                    <th>Subscribed On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subscribers as $sub): ?>
                <tr>
                    <td><?= htmlspecialchars($sub['email']) ?></td>
                    <td><span style="color:var(--primary-color); font-weight:600;">Active</span></td>
                    <td><?= date('d M Y', strtotime($sub['created_at'])) ?></td>
                    <td>
                        <button onclick="deleteSubscriber(<?= $sub['id'] ?>, this)"
                                style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:14px;" title="Remove">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Broadcast Form -->
    <div class="section-card" style="margin-top:30px;">
        <div class="section-header"><h4>Quick Broadcast</h4></div>
        <?php if($totalSubs === 0): ?>
        <p style="color:#94a3b8; font-size:13px;">You need at least one subscriber before sending a broadcast.</p>
        <?php else: ?>
        <form>
            <div class="form-group">
                <label>Campaign Title</label>
                <input type="text" class="form-control" placeholder="e.g. SCCDR | Monthly Update – April 2026">
            </div>
            <div class="form-group">
                <label>Recipients</label>
                <select class="form-control">
                    <option>All Subscribers (<?= $totalSubs ?>)</option>
                    <option>Recent Subscribers (Last 30 days)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Newsletter Content</label>
                <textarea class="form-control" rows="8" placeholder="Write newsletter body here…"></textarea>
            </div>
            <button type="button" class="btn-upload" style="width:100%;">
                <i class="fas fa-paper-plane"></i> Send Newsletter Blast
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
async function deleteSubscriber(id, btn) {
    if (!confirm('Remove this subscriber?')) return;
    const fd = new FormData(); fd.append('id', id);
    const res  = await fetch('/actions/delete_subscriber.php', { method:'POST', body:fd });
    const data = await res.json();
    if (data.status === 'success') { btn.closest('tr').remove(); }
}
</script>
