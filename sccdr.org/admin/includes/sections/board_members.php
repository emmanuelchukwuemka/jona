<?php
require_once __DIR__ . '/../../../includes/config.php';

// Ensure board_members table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `board_members` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(300) NOT NULL,
    `role` varchar(200) NOT NULL,
    `bio` text DEFAULT NULL,
    `photo` varchar(500) DEFAULT NULL,
    `sort_order` int(11) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$members = $pdo->query("SELECT * FROM board_members ORDER BY sort_order ASC, created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Board Members Management -->
<div id="section-board-members" class="admin-section" style="display:none;">

    <div class="section-header" style="margin-bottom:24px;">
        <h3>Board &amp; Team Management</h3>
        <span style="font-size:13px; color:#94a3b8;"><?= count($members) ?> member<?= count($members) !== 1 ? 's' : '' ?></span>
    </div>

    <!-- Current Members Grid -->
    <?php if(empty($members)): ?>
    <div style="background:#fff; border-radius:12px; padding:50px; text-align:center; color:#94a3b8; box-shadow:var(--shadow); margin-bottom:30px;">
        <i class="fas fa-users" style="font-size:40px; display:block; margin-bottom:14px; opacity:0.3;"></i>
        <h4 style="color:#cbd5e1; font-weight:700; margin-bottom:8px;">No board members yet</h4>
        <p style="font-size:13px;">Use the form below to add the first member.</p>
    </div>
    <?php else: ?>
    <div class="board-grid" style="margin-bottom:30px;">
        <?php foreach($members as $m): ?>
        <div class="board-card">
            <?php if($m['photo']): ?>
            <div class="board-avatar" style="background-image:url('<?= htmlspecialchars($m['photo']) ?>');"></div>
            <?php else: ?>
            <div class="board-avatar" style="background:linear-gradient(135deg,var(--primary-color),var(--secondary-color)); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; color:#fff;">
                <?= strtoupper(substr($m['name'],0,1)) ?>
            </div>
            <?php endif; ?>
            <h4><?= htmlspecialchars($m['name']) ?></h4>
            <div class="role"><?= htmlspecialchars($m['role']) ?></div>
            <div class="board-bio"><?= htmlspecialchars($m['bio'] ?? '') ?></div>
            <div class="board-actions">
                <button onclick="deleteBoardMember(<?= $m['id'] ?>, this)"
                        class="btn-icon" style="color:#ef4444; border-color:#fee2e2;">
                    <i class="fas fa-trash-alt"></i> Remove
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Add New Member Form -->
    <div class="section-card">
        <div class="section-header"><h3>Add New Board Member</h3></div>
        <div id="bm-alert" style="display:none; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px; font-weight:500;"></div>
        <form id="boardMemberForm" onsubmit="saveBoardMember(event)" enctype="multipart/form-data">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Prof. Jane Doe" required>
                </div>
                <div class="form-group">
                    <label>Position / Role *</label>
                    <input type="text" name="role" class="form-control" placeholder="e.g. Secretary" required>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Profile Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>
                <div class="form-group">
                    <label>Display Order <span style="font-weight:400; color:#94a3b8; font-size:11px;">(lower = first)</span></label>
                    <input type="number" name="sort_order" class="form-control" value="0" min="0">
                </div>
            </div>
            <div class="form-group">
                <label>Biography</label>
                <textarea name="bio" class="form-control" rows="4" placeholder="Enter a short professional bio…"></textarea>
            </div>
            <button type="submit" class="btn-upload" id="btnSaveMember">
                <i class="fas fa-user-plus"></i> Save Member Profile
            </button>
        </form>
    </div>
</div>

<script>
async function saveBoardMember(e) {
    e.preventDefault();
    const alert = document.getElementById('bm-alert');
    const btn   = document.getElementById('btnSaveMember');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    const fd = new FormData(document.getElementById('boardMemberForm'));
    try {
        const res  = await fetch('/actions/save_board_member.php', { method:'POST', body:fd });
        const data = await res.json();
        alert.style.display = 'block';
        alert.style.background  = data.status === 'success' ? 'rgba(122,208,58,0.1)' : 'rgba(239,68,68,0.08)';
        alert.style.color       = data.status === 'success' ? '#166534' : '#b91c1c';
        alert.style.border      = data.status === 'success' ? '1px solid rgba(122,208,58,0.25)' : '1px solid rgba(239,68,68,0.2)';
        alert.innerHTML = data.message;
        if (data.status === 'success') {
            document.getElementById('boardMemberForm').reset();
            setTimeout(() => location.reload(), 1200);
        }
    } catch(err) {
        alert.style.display = 'block'; alert.innerHTML = 'Network error.';
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-user-plus"></i> Save Member Profile';
}

async function deleteBoardMember(id, btn) {
    if (!confirm('Remove this board member permanently?')) return;
    const fd = new FormData(); fd.append('id', id);
    const res  = await fetch('/actions/delete_board_member.php', { method:'POST', body:fd });
    const data = await res.json();
    if (data.status === 'success') {
        btn.closest('.board-card').style.opacity = '0';
        btn.closest('.board-card').style.transition = 'opacity 0.3s';
        setTimeout(() => location.reload(), 350);
    }
}
</script>
