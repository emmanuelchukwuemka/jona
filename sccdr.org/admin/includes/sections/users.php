<?php
require_once '../../../../includes/config.php';

// Fetch all real users from DB
$allUsers = $pdo->query("SELECT id, first_name, last_name, email, membership_category, role, profile_picture, created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Users Registry Section -->
<div id="section-users" class="admin-section" style="display:none;">
    <div class="section-card">
        <div class="section-header">
            <h3>Registered Members &amp; Users <span style="font-size:14px; font-weight:500; color:#94a3b8;">(<?= count($allUsers) ?> total)</span></h3>
        </div>

        <?php if(empty($allUsers)): ?>
        <p style="color:#94a3b8; text-align:center; padding:40px;">No members registered yet.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Email Address</th>
                        <th>Category</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($allUsers as $u):
                        $initials = strtoupper(substr($u['first_name'],0,1) . substr($u['last_name'],0,1));
                        $colors   = ['#7AD03A','#3b82f6','#f59e0b','#8b5cf6','#ef4444','#14b8a6'];
                        $color    = $colors[crc32($u['email']) % count($colors)];
                    ?>
                    <tr>
                        <td>
                            <div class="user-badge">
                                <?php if($u['profile_picture']): ?>
                                    <img src="<?= htmlspecialchars($u['profile_picture']) ?>"
                                         alt="<?= htmlspecialchars($initials) ?>"
                                         style="width:36px; height:36px; border-radius:50%; object-fit:cover; flex-shrink:0; border:2px solid #e8edf5;">
                                <?php else: ?>
                                    <span class="user-avatar" style="background-color:<?= $color ?>;"><?= $initials ?></span>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight:600; color:#1e293b; font-size:14px;">
                                        <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span style="background:rgba(122,208,58,0.1); color:#144525; padding:3px 10px; border-radius:50px; font-size:12px; font-weight:600;">
                                <?= htmlspecialchars($u['membership_category']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if($u['role'] === 'admin'): ?>
                                <span style="background:rgba(239,68,68,0.1); color:#ef4444; padding:3px 10px; border-radius:50px; font-size:12px; font-weight:600;">Admin</span>
                            <?php else: ?>
                                <span style="background:rgba(59,130,246,0.1); color:#3b82f6; padding:3px 10px; border-radius:50px; font-size:12px; font-weight:600;">Member</span>
                            <?php endif; ?>
                        </td>
                        <td style="color:#64748b;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
