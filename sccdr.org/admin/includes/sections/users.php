<?php
require_once __DIR__ . '/../../../includes/config.php';

// Fetch all users including status
$allUsers = $pdo->query("
    SELECT id, first_name, last_name, email, institution,
           membership_category, role, status, profile_picture, created_at
    FROM users ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$totalActive    = count(array_filter($allUsers, fn($u) => ($u['status'] ?? 'active') === 'active'));
$totalSuspended = count(array_filter($allUsers, fn($u) => ($u['status'] ?? 'active') === 'suspended'));
?>

<!-- Users Registry Section -->
<div id="section-users" class="admin-section" style="display:none;">

    <!-- Stats row -->
    <div class="users-stats-row">
        <div class="u-stat-card">
            <div class="u-stat-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="fas fa-users"></i></div>
            <div class="u-stat-body">
                <span class="u-stat-number"><?= count($allUsers) ?></span>
                <span class="u-stat-label">Total Users</span>
            </div>
        </div>
        <div class="u-stat-card">
            <div class="u-stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981;"><i class="fas fa-user-check"></i></div>
            <div class="u-stat-body">
                <span class="u-stat-number"><?= $totalActive ?></span>
                <span class="u-stat-label">Active</span>
            </div>
        </div>
        <div class="u-stat-card">
            <div class="u-stat-icon" style="background:rgba(239,68,68,0.12);color:#ef4444;"><i class="fas fa-user-slash"></i></div>
            <div class="u-stat-body">
                <span class="u-stat-number"><?= $totalSuspended ?></span>
                <span class="u-stat-label">Suspended</span>
            </div>
        </div>
    </div>

    <div class="section-card" style="margin-top:20px;">
        <div class="section-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <h3>Registered Members &amp; Users</h3>
            <!-- Search -->
            <div class="users-search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="usersSearchInput" placeholder="Search by name, email or category…" oninput="filterUsersTable()">
            </div>
        </div>

        <?php if(empty($allUsers)): ?>
        <p style="color:#94a3b8;text-align:center;padding:40px;">No members registered yet.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table" id="usersTable">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Email</th>
                        <th>Category</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($allUsers as $u):
                        $initials = strtoupper(substr($u['first_name'],0,1) . substr($u['last_name'],0,1));
                        $colors   = ['#7AD03A','#3b82f6','#f59e0b','#8b5cf6','#ef4444','#14b8a6'];
                        $color    = $colors[crc32($u['email']) % count($colors)];
                        $status   = $u['status'] ?? 'active';
                        $isSusp   = $status === 'suspended';
                    ?>
                    <tr data-user-id="<?= $u['id'] ?>">
                        <td>
                            <div class="user-badge">
                                <?php if($u['profile_picture']): ?>
                                    <img src="<?= htmlspecialchars($u['profile_picture']) ?>"
                                         alt="<?= htmlspecialchars($initials) ?>"
                                         style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid #e8edf5;">
                                <?php else: ?>
                                    <span class="user-avatar" style="background-color:<?= $color ?>;flex-shrink:0;"><?= $initials ?></span>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight:600;color:#1e293b;font-size:14px;">
                                        <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?>
                                    </div>
                                    <?php if($u['institution']): ?>
                                    <div style="font-size:11px;color:#94a3b8;"><?= htmlspecialchars($u['institution']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td style="color:#475569;font-size:13px;"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge-category"><?= htmlspecialchars($u['membership_category']) ?></span>
                        </td>
                        <td>
                            <?php if($u['role'] === 'admin'): ?>
                                <span class="badge-role badge-admin">Admin</span>
                            <?php else: ?>
                                <span class="badge-role badge-member">Member</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge-status <?= $isSusp ? 'badge-suspended' : 'badge-active' ?>">
                                <i class="fas <?= $isSusp ? 'fa-ban' : 'fa-circle' ?>" style="font-size:8px;"></i>
                                <?= ucfirst($status) ?>
                            </span>
                        </td>
                        <td style="color:#64748b;font-size:13px;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <div class="user-actions-cell">
                                <button class="btn-user-action btn-view"
                                        title="View Details"
                                        onclick="openUserModal(<?= $u['id'] ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if($u['role'] !== 'admin'): ?>
                                    <?php if($isSusp): ?>
                                    <button class="btn-user-action btn-unsuspend"
                                            title="Unsuspend"
                                            data-id="<?= $u['id'] ?>"
                                            onclick="toggleSuspend(this, <?= $u['id'] ?>, 'unsuspend')">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                    <?php else: ?>
                                    <button class="btn-user-action btn-suspend"
                                            title="Suspend"
                                            data-id="<?= $u['id'] ?>"
                                            onclick="toggleSuspend(this, <?= $u['id'] ?>, 'suspend')">
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn-user-action btn-delete"
                                            title="Delete User"
                                            data-id="<?= $u['id'] ?>"
                                            onclick="deleteUserTableRow(this, <?= $u['id'] ?>)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                <?php else: ?>
                                    <span style="font-size:11px;color:#94a3b8;padding:0 4px;">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     USER DETAIL MODAL
════════════════════════════════════════════════════════════════════════════ -->
<div id="userDetailModal" class="modal-backdrop" style="display:none;" onclick="closeUserModal(event)">
    <div class="modal-box user-detail-modal" onclick="event.stopPropagation()">
        <button class="modal-close-btn" onclick="closeUserModal()"><i class="fas fa-times"></i></button>

        <div id="umdLoading" style="text-align:center;padding:60px 0;">
            <i class="fas fa-spinner fa-spin" style="font-size:28px;color:#7AD03A;"></i>
            <p style="color:#94a3b8;margin-top:12px;">Loading user details…</p>
        </div>

        <div id="umdContent" style="display:none;">
            <!-- Header / Avatar -->
            <div class="umd-header">
                <div id="umdAvatarWrap"></div>
                <div class="umd-header-info">
                    <h2 id="umdFullName"></h2>
                    <p id="umdEmail" style="color:#64748b;font-size:14px;margin:2px 0;"></p>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                        <span id="umdRoleBadge"></span>
                        <span id="umdStatusBadge"></span>
                        <span id="umdCategoryBadge"></span>
                    </div>
                </div>
            </div>

            <!-- Detail grid -->
            <div class="umd-detail-grid">
                <div class="umd-detail-item">
                    <span class="umd-detail-label"><i class="fas fa-university"></i> Institution</span>
                    <span id="umdInstitution" class="umd-detail-value"></span>
                </div>
                <div class="umd-detail-item">
                    <span class="umd-detail-label"><i class="fas fa-tag"></i> Membership Category</span>
                    <span id="umdCategory" class="umd-detail-value"></span>
                </div>
                <div class="umd-detail-item">
                    <span class="umd-detail-label"><i class="fas fa-microscope"></i> Abstracts Submitted</span>
                    <span id="umdAbstracts" class="umd-detail-value"></span>
                </div>
                <div class="umd-detail-item">
                    <span class="umd-detail-label"><i class="fas fa-calendar-plus"></i> Registered On</span>
                    <span id="umdJoined" class="umd-detail-value"></span>
                </div>
            </div>

            <!-- Modal action footer -->
            <div class="umd-footer" id="umdActionFooter"></div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════════
     USERS CSS
════════════════════════════════════════════════════════════════════════════ -->
<style>
/* ── Stats row ── */
.users-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 4px;
}
.u-stat-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e8edf5;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.u-stat-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.u-stat-number { display:block; font-size:24px; font-weight:700; color:#1e293b; line-height:1; }
.u-stat-label  { display:block; font-size:11px; color:#94a3b8; margin-top:3px; font-weight:500; text-transform:uppercase; letter-spacing:.5px; }

/* ── Search ── */
.users-search-wrap {
    position: relative;
    display: flex; align-items: center;
}
.users-search-wrap i {
    position: absolute; left: 12px; color: #94a3b8; font-size: 13px;
}
.users-search-wrap input {
    padding: 8px 14px 8px 34px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    color: #1e293b;
    background: #f8fafc;
    outline: none;
    width: 280px;
    transition: border-color .2s, box-shadow .2s;
}
.users-search-wrap input:focus {
    border-color: #7AD03A;
    box-shadow: 0 0 0 3px rgba(122,208,58,.12);
    background: #fff;
}

/* ── Badges ── */
.badge-category {
    background: rgba(122,208,58,.1); color: #166534;
    padding: 3px 10px; border-radius: 50px; font-size: 12px; font-weight: 600;
}
.badge-role {
    padding: 3px 10px; border-radius: 50px; font-size: 12px; font-weight: 600;
}
.badge-admin  { background: rgba(239,68,68,.1);  color: #ef4444; }
.badge-member { background: rgba(59,130,246,.1); color: #3b82f6; }

.badge-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 50px; font-size: 12px; font-weight: 600;
}
.badge-active    { background: rgba(16,185,129,.1); color: #10b981; }
.badge-suspended { background: rgba(239,68,68,.1);  color: #ef4444; }

/* ── Action buttons ── */
.user-actions-cell {
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-user-action {
    width: 32px; height: 32px;
    border: none; border-radius: 8px;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    transition: transform .15s, opacity .15s;
}
.btn-user-action:hover { transform: scale(1.1); }

.btn-view      { background: rgba(59,130,246,.12); color: #3b82f6; }
.btn-suspend   { background: rgba(239,68,68,.12);  color: #ef4444; }
.btn-unsuspend { background: rgba(16,185,129,.12); color: #10b981; }
.btn-delete    { background: rgba(239,68,68,.12);  color: #ef4444; }

/* ── Modal backdrop ── */
.modal-backdrop {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(15,23,42,.55);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}
.modal-box {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 24px 80px rgba(0,0,0,.2);
    position: relative;
    animation: modalIn .25s ease;
    max-height: 90vh;
    overflow-y: auto;
}
@keyframes modalIn {
    from { opacity:0; transform:translateY(24px) scale(.97); }
    to   { opacity:1; transform:translateY(0)    scale(1);   }
}
.modal-close-btn {
    position: absolute; top: 16px; right: 16px;
    width: 32px; height: 32px;
    background: #f1f5f9; border: none; border-radius: 50%;
    cursor: pointer; color: #64748b; font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s, color .2s;
}
.modal-close-btn:hover { background: #ef4444; color: #fff; }

/* ── User detail modal specifics ── */
.user-detail-modal { width: 100%; max-width: 560px; padding: 32px; }

.umd-header {
    display: flex; gap: 18px; align-items: flex-start;
    padding-bottom: 24px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 24px;
}
#umdAvatarWrap img,
#umdAvatarWrap .umd-initials {
    width: 72px; height: 72px; border-radius: 18px;
    object-fit: cover; flex-shrink: 0;
}
.umd-initials {
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; font-weight: 700; color: #fff;
    border-radius: 18px; flex-shrink: 0;
}
.umd-header-info h2 { margin: 0 0 4px; font-size: 20px; color: #0f172a; font-weight: 700; }

/* ── Detail grid ── */
.umd-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 28px;
}
.umd-detail-item {
    background: #f8fafc; border-radius: 12px; padding: 14px 16px;
    display: flex; flex-direction: column; gap: 6px;
}
.umd-detail-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
.umd-detail-label i { margin-right: 5px; }
.umd-detail-value { font-size: 14px; color: #1e293b; font-weight: 600; }

/* ── Modal footer ── */
.umd-footer {
    display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}
.umd-btn {
    padding: 10px 22px; border-radius: 10px; border: none;
    font-size: 14px; font-weight: 600; cursor: pointer;
    display: inline-flex; align-items: center; gap: 8px;
    transition: opacity .2s, transform .15s;
}
.umd-btn:hover { opacity: .87; transform: translateY(-1px); }
.umd-btn-suspend   { background: #ef4444; color: #fff; }
.umd-btn-unsuspend { background: #10b981; color: #fff; }
.umd-btn-cancel    { background: #f1f5f9; color: #475569; }
.umd-btn-delete    { background: #dc2626; color: #fff; }

@media (max-width:560px) {
    .umd-detail-grid { grid-template-columns: 1fr; }
    .umd-header { flex-direction: column; }
    .users-search-wrap input { width: 100%; }
}
</style>

<!-- ═══════════════════════════════════════════════════════════════════════════
     USERS JS
════════════════════════════════════════════════════════════════════════════ -->
<script>
/* ────────────────── Table search filter ────────────────── */
function filterUsersTable() {
    const q = document.getElementById('usersSearchInput').value.toLowerCase();
    document.querySelectorAll('#usersTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

/* ────────────────── Open modal ────────────────── */
function openUserModal(userId) {
    const modal   = document.getElementById('userDetailModal');
    const loading = document.getElementById('umdLoading');
    const content = document.getElementById('umdContent');

    modal.style.display   = 'flex';
    loading.style.display = 'block';
    content.style.display = 'none';
    modal.dataset.userId  = userId;

    fetch('/actions/manage_user.php?action=get_details&user_id=' + userId)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { alert(data.message); closeUserModal(); return; }
            populateUserModal(data.user);
            loading.style.display = 'none';
            content.style.display = 'block';
        })
        .catch(() => {
            alert('Failed to load user details. Please try again.');
            closeUserModal();
        });
}

/* ────────────────── Populate modal ────────────────── */
function populateUserModal(u) {
    const avatarWrap = document.getElementById('umdAvatarWrap');
    const colors     = ['#7AD03A','#3b82f6','#f59e0b','#8b5cf6','#ef4444','#14b8a6'];
    const color      = colors[Math.abs(hashCode(u.email)) % colors.length];
    const initials   = (u.first_name[0] + u.last_name[0]).toUpperCase();

    if (u.profile_picture) {
        avatarWrap.innerHTML = `<img src="${escHtml(u.profile_picture)}" alt="${initials}">`;
    } else {
        avatarWrap.innerHTML = `<div class="umd-initials" style="background:${color};">${initials}</div>`;
    }

    document.getElementById('umdFullName').textContent  = u.first_name + ' ' + u.last_name;
    document.getElementById('umdEmail').textContent     = u.email;
    document.getElementById('umdInstitution').textContent  = u.institution || '—';
    document.getElementById('umdCategory').textContent     = u.membership_category;
    document.getElementById('umdAbstracts').textContent    = u.abstract_count;
    document.getElementById('umdJoined').textContent       = u.created_at_fmt;

    // Role badge
    const roleBadge = document.getElementById('umdRoleBadge');
    roleBadge.className = 'badge-role ' + (u.role === 'admin' ? 'badge-admin' : 'badge-member');
    roleBadge.textContent = u.role === 'admin' ? 'Admin' : 'Member';

    // Status badge
    const stBadge = document.getElementById('umdStatusBadge');
    const isSusp  = u.status === 'suspended';
    stBadge.className   = 'badge-status ' + (isSusp ? 'badge-suspended' : 'badge-active');
    stBadge.innerHTML   = `<i class="fas ${isSusp ? 'fa-ban' : 'fa-circle'}" style="font-size:8px;"></i> ${capitalize(u.status)}`;

    // Category badge
    const catBadge = document.getElementById('umdCategoryBadge');
    catBadge.className   = 'badge-category';
    catBadge.textContent = u.membership_category;

    // Footer action buttons
    const footer = document.getElementById('umdActionFooter');
    if (u.role !== 'admin') {
        const suspBtn = isSusp
            ? `<button class="umd-btn umd-btn-unsuspend" onclick="modalToggleSuspend(${u.id}, 'unsuspend')"><i class="fas fa-user-check"></i> Reactivate Account</button>`
            : `<button class="umd-btn umd-btn-suspend"   onclick="modalToggleSuspend(${u.id}, 'suspend')"><i class="fas fa-user-slash"></i> Suspend User</button>`;
        footer.innerHTML = `<button class="umd-btn umd-btn-cancel" onclick="closeUserModal()"><i class="fas fa-times"></i> Close</button>
                            ${suspBtn}
                            <button class="umd-btn umd-btn-delete" onclick="modalDeleteUser(${u.id})"><i class="fas fa-trash-alt"></i> Delete</button>`;
    } else {
        footer.innerHTML = `<button class="umd-btn umd-btn-cancel" onclick="closeUserModal()"><i class="fas fa-times"></i> Close</button>`;
    }
}

/* ────────────────── Close modal ────────────────── */
function closeUserModal(event) {
    if (event && event.target !== document.getElementById('userDetailModal')) return;
    document.getElementById('userDetailModal').style.display = 'none';
}

/* ────────────────── Delete User ────────────────── */
function deleteUserTableRow(btn, userId) {
    if (!confirm('WARNING: Are you sure you want to PERMANENTLY delete this user?\nAll their associated records (except where NULL is allowed) will be affected.')) return;
    const fd = new FormData();
    fd.append('action', 'delete');
    fd.append('user_id', userId);

    fetch('/actions/manage_user.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { alert(data.message); return; }
            const row = document.querySelector(`#usersTable tr[data-user-id="${userId}"]`);
            if (row) row.remove();
            
            // Recompute counts and update stats cards manually here if needed.
            // Simplified: reload the page to refresh stats properly in admin mode.
            location.reload();
        });
}

function modalDeleteUser(userId) {
    if (!confirm('WARNING: Are you sure you want to PERMANENTLY delete this user?')) return;
    const fd = new FormData();
    fd.append('action', 'delete');
    fd.append('user_id', userId);

    fetch('/actions/manage_user.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { alert(data.message); return; }
            location.reload(); // Refresh to update row and stats
        });
}

/* ────────────────── Toggle suspend from table row ────────────────── */
function toggleSuspend(btn, userId, action) {
    if (!confirm(action === 'suspend'
            ? 'Are you sure you want to suspend this user?'
            : 'Reactivate this user account?')) return;

    const fd = new FormData();
    fd.append('action', action);
    fd.append('user_id', userId);

    fetch('/actions/manage_user.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { alert(data.message); return; }
            // Update the row badge & button in place
            const row     = document.querySelector(`#usersTable tr[data-user-id="${userId}"]`);
            if (!row) { location.reload(); return; }
            const stCell  = row.cells[4];
            const isSusp  = data.status === 'suspended';
            stCell.innerHTML = `<span class="badge-status ${isSusp ? 'badge-suspended' : 'badge-active'}">
                <i class="fas ${isSusp ? 'fa-ban' : 'fa-circle'}" style="font-size:8px;"></i> ${capitalize(data.status)}
            </span>`;
            // Swap button
            const actCell = row.cells[6].querySelector('.user-actions-cell');
            const newBtn  = document.createElement('button');
            newBtn.className = `btn-user-action ${isSusp ? 'btn-unsuspend' : 'btn-suspend'}`;
            newBtn.title     = isSusp ? 'Unsuspend' : 'Suspend';
            newBtn.dataset.id = userId;
            newBtn.innerHTML = `<i class="fas ${isSusp ? 'fa-user-check' : 'fa-user-slash'}"></i>`;
            newBtn.onclick   = () => toggleSuspend(newBtn, userId, isSusp ? 'unsuspend' : 'suspend');
            actCell.querySelector('.btn-suspend, .btn-unsuspend')?.replaceWith(newBtn);

            // Update stats counters in the stat cards
            refreshUserStats();
        })
        .catch(() => alert('Request failed. Please try again.'));
}

/* ────────────────── Toggle suspend from modal ────────────────── */
function modalToggleSuspend(userId, action) {
    if (!confirm(action === 'suspend'
            ? 'Are you sure you want to suspend this user?'
            : 'Reactivate this user account?')) return;

    const fd = new FormData();
    fd.append('action', action);
    fd.append('user_id', userId);

    fetch('/actions/manage_user.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { alert(data.message); return; }
            // Re-fetch & repopulate modal
            fetch('/actions/manage_user.php?action=get_details&user_id=' + userId)
                .then(r => r.json())
                .then(d => { if (d.success) populateUserModal(d.user); });
            // Also update the table row
            const row = document.querySelector(`#usersTable tr[data-user-id="${userId}"]`);
            if (row) {
                const isSusp  = data.status === 'suspended';
                row.cells[4].innerHTML = `<span class="badge-status ${isSusp ? 'badge-suspended' : 'badge-active'}">
                    <i class="fas ${isSusp ? 'fa-ban' : 'fa-circle'}" style="font-size:8px;"></i> ${capitalize(data.status)}
                </span>`;
                const actCell = row.cells[6].querySelector('.user-actions-cell');
                const newBtn  = document.createElement('button');
                newBtn.className = `btn-user-action ${isSusp ? 'btn-unsuspend' : 'btn-suspend'}`;
                newBtn.title     = isSusp ? 'Unsuspend' : 'Suspend';
                newBtn.dataset.id = userId;
                newBtn.innerHTML = `<i class="fas ${isSusp ? 'fa-user-check' : 'fa-user-slash'}"></i>`;
                newBtn.onclick   = () => toggleSuspend(newBtn, userId, isSusp ? 'unsuspend' : 'suspend');
                actCell.querySelector('.btn-suspend, .btn-unsuspend')?.replaceWith(newBtn);
                refreshUserStats();
            }
        })
        .catch(() => alert('Request failed. Please try again.'));
}

/* ────────────────── Recalculate stat cards without page reload ────────────────── */
function refreshUserStats() {
    let active = 0, suspended = 0;
    document.querySelectorAll('#usersTable tbody tr').forEach(row => {
        const badge = row.cells[4]?.querySelector('.badge-status');
        if (!badge) return;
        if (badge.classList.contains('badge-suspended')) suspended++;
        else active++;
    });
    const cards = document.querySelectorAll('#section-users .u-stat-number');
    if (cards[0]) cards[0].textContent = active + suspended;
    if (cards[1]) cards[1].textContent = active;
    if (cards[2]) cards[2].textContent = suspended;
}

/* ────────────────── Helpers ────────────────── */
function capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
function escHtml(s)    { const d=document.createElement('div');d.textContent=s;return d.innerHTML; }
function hashCode(str) {
    let h = 0;
    for (let i = 0; i < str.length; i++) { h = Math.imul(31, h) + str.charCodeAt(i) | 0; }
    return h;
}
</script>
