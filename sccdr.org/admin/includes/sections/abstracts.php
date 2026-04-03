<?php
require_once __DIR__ . '/../../../includes/config.php';

// Ensure abstracts table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `abstracts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `title` varchar(500) NOT NULL,
    `authors` varchar(500) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'General',
    `abstract_text` text NOT NULL,
    `status` ENUM('submitted','review','accepted','rejected') NOT NULL DEFAULT 'submitted',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Fetch all abstracts, join with user for name/picture
$allAbstracts = $pdo->query("
    SELECT a.*, 
           u.first_name, u.last_name, u.email, u.profile_picture
    FROM abstracts a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Group by status
$grouped = ['submitted' => [], 'review' => [], 'accepted' => [], 'rejected' => []];
foreach ($allAbstracts as $ab) {
    $grouped[$ab['status']][] = $ab;
}

$columns = [
    'submitted' => ['label' => 'Submitted',    'color' => '#3b82f6', 'icon' => 'fa-inbox'],
    'review'    => ['label' => 'Under Review', 'color' => '#f59e0b', 'icon' => 'fa-search'],
    'accepted'  => ['label' => 'Accepted',     'color' => '#7AD03A', 'icon' => 'fa-check-circle'],
    'rejected'  => ['label' => 'Rejected',     'color' => '#ef4444', 'icon' => 'fa-times-circle'],
];
?>

<!-- ═══ Admin: Abstract Tracking (Live Kanban) ═══ -->
<div id="section-abstracts" class="admin-section" style="display:none;">

    <!-- Header -->
    <div class="section-header" style="margin-bottom: 24px;">
        <h3>
            <i class="fas fa-microscope" style="color: var(--primary-color); margin-right: 8px;"></i>
            Abstract Submissions
            <span style="font-size:14px; font-weight:500; color:#94a3b8; margin-left:8px;">(<?= count($allAbstracts) ?> total)</span>
        </h3>
        <div style="display:flex; align-items:center; gap:12px;">
            <input type="text" id="abstractSearch" placeholder="Search abstracts…"
                   oninput="filterAbstracts(this.value)"
                   style="border:1px solid var(--border-color); border-radius:8px; padding:9px 14px; font-size:13px; font-family:inherit; outline:none; width:220px;">
        </div>
    </div>

    <?php if(empty($allAbstracts)): ?>
    <div style="background:#fff; border-radius:12px; padding:60px; text-align:center; color:#94a3b8; box-shadow: var(--shadow);">
        <i class="fas fa-inbox" style="font-size:48px; margin-bottom:16px; display:block; opacity:0.4;"></i>
        <h4 style="color:#cbd5e1; font-weight:700; font-size:18px; margin-bottom:8px;">No abstracts submitted yet</h4>
        <p style="font-size:14px;">When members submit abstracts via their dashboard, they will appear here.</p>
    </div>
    <?php else: ?>

    <!-- Kanban Board -->
    <div class="kanban-board" id="kanbanBoard">
        <?php foreach($columns as $status => $col): ?>
        <div class="kanban-col" id="col-<?= $status ?>">
            <!-- Column Header -->
            <div class="kanban-header">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class="fas <?= $col['icon'] ?>" style="color:<?= $col['color'] ?>; font-size:14px;"></i>
                    <h4 style="color:<?= $col['color'] ?>;"><?= $col['label'] ?></h4>
                </div>
                <span class="kanban-count" style="background:<?= $col['color'] ?>1a; color:<?= $col['color'] ?>;">
                    <?= count($grouped[$status]) ?>
                </span>
            </div>

            <!-- Cards -->
            <?php if(empty($grouped[$status])): ?>
            <div style="text-align:center; padding:30px 15px; color:#cbd5e1; font-size:13px; border:2px dashed #e8edf5; border-radius:10px;">
                <i class="fas fa-inbox" style="display:block; font-size:24px; margin-bottom:8px; opacity:0.4;"></i>
                No <?= strtolower($col['label']) ?> abstracts
            </div>
            <?php else: ?>
            <?php foreach($grouped[$status] as $ab):
                $initials = $ab['first_name']
                    ? strtoupper(substr($ab['first_name'],0,1).substr($ab['last_name'],0,1))
                    : '?';
                $submitterName = $ab['first_name']
                    ? htmlspecialchars($ab['first_name'].' '.$ab['last_name'])
                    : htmlspecialchars($ab['authors']);
            ?>
            <div class="abstract-card <?= $status ?>" data-id="<?= $ab['id'] ?>"
                 data-title="<?= htmlspecialchars(strtolower($ab['title'])) ?>"
                 data-author="<?= htmlspecialchars(strtolower($ab['authors'])) ?>">

                <!-- Category badge -->
                <span style="display:inline-block; background:<?= $col['color'] ?>1a; color:<?= $col['color'] ?>; font-size:10px; font-weight:700; padding:3px 10px; border-radius:50px; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.5px;">
                    <?= htmlspecialchars($ab['category']) ?>
                </span>

                <!-- Title -->
                <h5><?= htmlspecialchars(strlen($ab['title']) > 80 ? substr($ab['title'],0,80).'…' : $ab['title']) ?></h5>

                <!-- Author row -->
                <div style="display:flex; align-items:center; gap:8px; margin:10px 0 6px;">
                    <?php if(!empty($ab['profile_picture'])): ?>
                    <img src="<?= htmlspecialchars($ab['profile_picture']) ?>" alt="<?= $initials ?>"
                         style="width:26px; height:26px; border-radius:50%; object-fit:cover; flex-shrink:0;">
                    <?php else: ?>
                    <div style="width:26px; height:26px; border-radius:50%; background:var(--primary-color); color:#fff; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0;">
                        <?= $initials ?>
                    </div>
                    <?php endif; ?>
                    <span style="font-size:12px; font-weight:600; color:var(--heading-color);"><?= $submitterName ?></span>
                </div>

                <!-- Abstract preview -->
                <p style="font-size:12px; color:#94a3b8; line-height:1.6; margin-bottom:12px;">
                    <?= htmlspecialchars(substr($ab['abstract_text'], 0, 110)) ?>…
                </p>

                <!-- Footer: date + status changer -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding-top:10px; border-top:1px solid var(--border-color);">
                    <span style="font-size:11px; color:#b0bec5;">
                        <i class="fas fa-calendar-alt" style="margin-right:4px;"></i>
                        <?= date('d M Y', strtotime($ab['created_at'])) ?>
                    </span>
                    <select onchange="updateAbstractStatus(<?= $ab['id'] ?>, this.value, this)"
                            style="font-size:11px; font-weight:700; border:1px solid var(--border-color); border-radius:6px; padding:4px 8px; cursor:pointer; background:#fff; color:var(--heading-color); font-family:inherit;">
                        <option value="submitted" <?= $status==='submitted' ? 'selected' : '' ?>>Submitted</option>
                        <option value="review"    <?= $status==='review'    ? 'selected' : '' ?>>Under Review</option>
                        <option value="accepted"  <?= $status==='accepted'  ? 'selected' : '' ?>>Accepted</option>
                        <option value="rejected"  <?= $status==='rejected'  ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>

                <!-- View full abstract toggle -->
                <div style="margin-top:10px;">
                    <button onclick="toggleAbstractDetail(this)"
                            style="background:none; border:none; color:var(--primary-color); font-size:12px; font-weight:700; cursor:pointer; padding:0; font-family:inherit;">
                        <i class="fas fa-chevron-down" style="margin-right:4px;"></i> View Full Abstract
                    </button>
                    <div class="abstract-full-text" style="display:none; margin-top:10px; padding:12px; background:#f8fafc; border-radius:8px; font-size:12.5px; line-height:1.7; color:#475569;">
                        <strong>Authors:</strong> <?= htmlspecialchars($ab['authors']) ?><br><br>
                        <?= nl2br(htmlspecialchars($ab['abstract_text'])) ?>
                        <?php if($ab['email']): ?>
                        <br><br><strong>Submitted by:</strong>
                        <a href="mailto:<?= htmlspecialchars($ab['email']) ?>" style="color:var(--primary-color);"><?= htmlspecialchars($ab['email']) ?></a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<script>
// ── Update abstract status via AJAX ──────────────────────────────────────────
async function updateAbstractStatus(id, newStatus, selectEl) {
    const card = selectEl.closest('.abstract-card');
    selectEl.disabled = true;

    const fd = new FormData();
    fd.append('id', id);
    fd.append('status', newStatus);

    try {
        const res  = await fetch('/actions/update_abstract_status.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.status === 'success') {
            // Brief flash then reload to re-sort the kanban
            card.style.opacity = '0.4';
            card.style.transition = 'opacity 0.3s';
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error updating status: ' + data.message);
            selectEl.disabled = false;
        }
    } catch (e) {
        alert('Network error. Please try again.');
        selectEl.disabled = false;
    }
}

// ── Toggle full abstract text ─────────────────────────────────────────────────
function toggleAbstractDetail(btn) {
    const detail = btn.nextElementSibling;
    const isOpen = detail.style.display !== 'none';
    detail.style.display = isOpen ? 'none' : 'block';
    btn.innerHTML = isOpen
        ? '<i class="fas fa-chevron-down" style="margin-right:4px;"></i> View Full Abstract'
        : '<i class="fas fa-chevron-up" style="margin-right:4px;"></i> Collapse';
}

// ── Search / filter cards ─────────────────────────────────────────────────────
function filterAbstracts(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#kanbanBoard .abstract-card').forEach(card => {
        const title  = card.dataset.title  || '';
        const author = card.dataset.author || '';
        card.style.display = (title.includes(q) || author.includes(q)) ? '' : 'none';
    });
}
</script>
