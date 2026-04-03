<?php
session_start();

// Security: kick out unauthenticated users
if (!isset($_SESSION['user_id'])) {
    header("Location: /membership.php");
    exit;
}

// Redirect admins to admin panel
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: /admin/index.php");
    exit;
}

require_once 'includes/config.php';

// Ensure journals table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `journals` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(500) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'Uncategorized',
    `abstract` text DEFAULT NULL,
    `file_path` varchar(500) NOT NULL,
    `uploaded_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Ensure abstracts table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `abstracts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `title` varchar(500) NOT NULL,
    `authors` varchar(500) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'General',
    `abstract_text` text NOT NULL,
    `status` ENUM('submitted','review','accepted','rejected') NOT NULL DEFAULT 'submitted',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: /membership.php");
    exit;
}

// Fetch all journals
$journals = $pdo->query("SELECT * FROM journals ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch member's own abstracts
$stmt2 = $pdo->prepare("SELECT * FROM abstracts WHERE user_id = ? ORDER BY created_at DESC");
$stmt2->execute([$_SESSION['user_id']]);
$myAbstracts = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
$memberSince = date('F Y', strtotime($user['created_at']));

// Count stats
$totalJournals = count($journals);
$totalAbstracts = count($myAbstracts);
$pendingAbstracts = count(array_filter($myAbstracts, fn($a) => in_array($a['status'], ['submitted','review'])));
$acceptedAbstracts = count(array_filter($myAbstracts, fn($a) => $a['status'] === 'accepted'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Portal – SCCDR</title>
    <meta name="description" content="SCCDR Member Control Panel – Access journals, submit abstracts, and manage your profile.">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" href="/member/css/style.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="member-layout">

    <!-- ══════════════ SIDEBAR ══════════════ -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-logo">
            <img src="/assets/img/logo.png" alt="SCCDR Logo">
        </div>

        <div class="sidebar-content">

            <div class="nav-label">Member Console</div>

            <a class="nav-item active" data-section="overview" id="nav-overview">
                <i class="fas fa-th-large"></i>
                <span>Overview</span>
            </a>

            <div class="nav-label">Research & Publications</div>

            <a class="nav-item" data-section="journals" id="nav-journals">
                <i class="fas fa-book-open"></i>
                <span>Research Journals</span>
                <?php if($totalJournals > 0): ?>
                <span class="badge"><?= $totalJournals ?></span>
                <?php endif; ?>
            </a>

            <a class="nav-item" data-section="submit-abstract" id="nav-submit-abstract">
                <i class="fas fa-file-signature"></i>
                <span>Submit Abstract</span>
            </a>

            <a class="nav-item" data-section="my-abstracts" id="nav-my-abstracts">
                <i class="fas fa-microscope"></i>
                <span>My Abstracts</span>
                <?php if($pendingAbstracts > 0): ?>
                <span class="badge"><?= $pendingAbstracts ?></span>
                <?php endif; ?>
            </a>

            <div class="nav-label">Quick Links</div>

            <a class="nav-item" href="/journals.php" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>JCCR Journal Page</span>
            </a>

            <a class="nav-item" href="/board-members.php" target="_blank">
                <i class="fas fa-users"></i>
                <span>Board Members</span>
            </a>

            <a class="nav-item" href="/contact-us.php" target="_blank">
                <i class="fas fa-envelope"></i>
                <span>Contact SCCDR</span>
            </a>

            <div class="nav-label">My Account</div>

            <a class="nav-item" data-section="profile" id="nav-profile">
                <i class="fas fa-user-circle"></i>
                <span>My Profile</span>
            </a>

        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <?php if(!empty($user['profile_picture'])): ?>
                <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="<?= $initials ?>"
                     style="width:40px; height:40px; border-radius:10px; object-fit:cover; flex-shrink:0; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                <?php else: ?>
                <div class="sidebar-avatar"><?= $initials ?></div>
                <?php endif; ?>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                    <div class="sidebar-user-role"><?= htmlspecialchars($user['membership_category']) ?></div>
                </div>
            </div>
            <div class="sidebar-actions">
                <a href="/" class="sidebar-action" title="View Website">
                    <i class="fas fa-globe"></i> <span>Site</span>
                </a>
                <a href="/actions/logout.php" class="sidebar-action logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </div>

    </aside>
    <!-- ══════════════ END SIDEBAR ══════════════ -->


    <!-- ══════════════ MAIN CONTENT ══════════════ -->
    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="topbar-title" id="topbarTitle">Overview</div>
                    <div class="topbar-breadcrumb">
                        Member Portal &rsaquo; <span id="topbarSection">Dashboard</span>
                    </div>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-greeting">
                    Welcome back, <strong><?= htmlspecialchars($user['first_name']) ?></strong>
                </div>
                <?php if(!empty($user['profile_picture'])): ?>
                <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="<?= $initials ?>"
                     style="width:38px; height:38px; border-radius:10px; object-fit:cover; cursor:pointer;">
                <?php else: ?>
                <div class="topbar-avatar"><?= $initials ?></div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Content sections -->
        <div class="content-wrapper">

            <!-- ═══ SECTION: OVERVIEW ═══ -->
            <div class="member-section active" id="section-overview">

                <!-- Stats Row -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(122,208,58,0.12);">
                            <i class="fas fa-book-open" style="color:#7AD03A;"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?= $totalJournals ?></h4>
                            <p>Journals Available</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(59,130,246,0.1);">
                            <i class="fas fa-file-alt" style="color:#3b82f6;"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?= $totalAbstracts ?></h4>
                            <p>Abstracts Submitted</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(245,158,11,0.1);">
                            <i class="fas fa-hourglass-half" style="color:#f59e0b;"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?= $pendingAbstracts ?></h4>
                            <p>Under Review</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(122,208,58,0.1);">
                            <i class="fas fa-check-circle" style="color:#7AD03A;"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?= $acceptedAbstracts ?></h4>
                            <p>Accepted</p>
                        </div>
                    </div>
                </div>

                <!-- Two-col layout: Latest Journals + Quick Access -->
                <div style="display:grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start;">

                    <!-- Latest Journals preview -->
                    <div class="section-card">
                        <div class="section-card-header">
                            <h3><i class="fas fa-book-open" style="color:#7AD03A; margin-right:8px;"></i> Latest Publications</h3>
                            <a href="#" class="nav-item-link" data-section="journals" style="font-size:13px; color:#7AD03A; font-weight:700; text-decoration:none;">View All →</a>
                        </div>
                        <?php if(empty($journals)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h4>No journals yet</h4>
                            <p>Check back soon — publications will appear here.</p>
                        </div>
                        <?php else: ?>
                        <div class="journal-grid">
                            <?php foreach(array_slice($journals, 0, 4) as $j): ?>
                            <div class="journal-card">
                                <span class="j-category"><?= htmlspecialchars($j['category']) ?></span>
                                <h4><?= htmlspecialchars(strlen($j['title']) > 75 ? substr($j['title'],0,75).'…' : $j['title']) ?></h4>
                                <?php if($j['abstract']): ?>
                                <p class="j-abstract"><?= htmlspecialchars(substr($j['abstract'],0,100)) ?>…</p>
                                <?php endif; ?>
                                <div class="j-footer">
                                    <span class="j-date"><i class="fas fa-calendar-alt" style="margin-right:4px;"></i><?= date('d M Y', strtotime($j['created_at'])) ?></span>
                                    <a href="<?= htmlspecialchars($j['file_path']) ?>" target="_blank" class="btn-download">
                                        <i class="fas fa-download"></i> PDF
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Access panel -->
                    <div style="display:flex; flex-direction:column; gap:14px;">

                        <div class="section-card" style="margin:0;">
                            <div style="font-size:13px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">Quick Access</div>
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                <a data-section="journals" class="nav-item-link" style="display:flex; align-items:center; gap:12px; padding:12px 14px; background:#f8fafc; border-radius:10px; text-decoration:none; color:#1e293b; font-size:13.5px; font-weight:600; transition:background 0.2s; border: 1px solid #e8edf5;">
                                    <div style="width:36px; height:36px; background:rgba(122,208,58,0.12); border-radius:9px; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-book-open" style="color:#7AD03A;"></i>
                                    </div>
                                    Browse Journals
                                </a>
                                <a data-section="submit-abstract" class="nav-item-link" style="display:flex; align-items:center; gap:12px; padding:12px 14px; background:#f8fafc; border-radius:10px; text-decoration:none; color:#1e293b; font-size:13.5px; font-weight:600; transition:background 0.2s; border: 1px solid #e8edf5;">
                                    <div style="width:36px; height:36px; background:rgba(59,130,246,0.1); border-radius:9px; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-file-signature" style="color:#3b82f6;"></i>
                                    </div>
                                    Submit Research Abstract
                                </a>
                                <a data-section="my-abstracts" class="nav-item-link" style="display:flex; align-items:center; gap:12px; padding:12px 14px; background:#f8fafc; border-radius:10px; text-decoration:none; color:#1e293b; font-size:13.5px; font-weight:600; transition:background 0.2s; border: 1px solid #e8edf5;">
                                    <div style="width:36px; height:36px; background:rgba(245,158,11,0.1); border-radius:9px; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-clock" style="color:#f59e0b;"></i>
                                    </div>
                                    Track My Abstracts
                                </a>
                                <a data-section="profile" class="nav-item-link" style="display:flex; align-items:center; gap:12px; padding:12px 14px; background:#f8fafc; border-radius:10px; text-decoration:none; color:#1e293b; font-size:13.5px; font-weight:600; transition:background 0.2s; border: 1px solid #e8edf5;">
                                    <div style="width:36px; height:36px; background:rgba(139,92,246,0.1); border-radius:9px; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-user-circle" style="color:#8b5cf6;"></i>
                                    </div>
                                    My Profile
                                </a>
                            </div>
                        </div>

                        <!-- Membership card -->
                        <div style="background: linear-gradient(135deg,#081e0f,#1a6030); border-radius:var(--radius); padding:22px; color:#fff;">
                            <div style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; opacity:0.6; margin-bottom:8px;">Membership Status</div>
                            <div style="font-size:18px; font-weight:800; margin-bottom:4px;"><?= htmlspecialchars($user['membership_category']) ?></div>
                            <div style="opacity:0.55; font-size:12px;">Member since <?= $memberSince ?></div>
                            <div style="margin-top:14px; display:flex; align-items:center; gap:6px;">
                                <div style="width:8px; height:8px; background:#7AD03A; border-radius:50%;"></div>
                                <span style="font-size:12px; font-weight:600; color:#a6e87a;">Active</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- ═════════════════════════════════ -->


            <!-- ═══ SECTION: JOURNALS ═══ -->
            <div class="member-section" id="section-journals">
                <div class="section-card">
                    <div class="section-card-header">
                        <h3><i class="fas fa-book-open" style="color:#7AD03A; margin-right:8px;"></i> Research Journals</h3>
                        <span style="font-size:13px; color:#94a3b8;"><?= $totalJournals ?> publication<?= $totalJournals !== 1 ? 's' : '' ?></span>
                    </div>

                    <?php if(empty($journals)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h4>No publications yet</h4>
                        <p>Research journals will appear here once published by the team.</p>
                    </div>
                    <?php else: ?>
                    <div class="journal-grid">
                        <?php foreach($journals as $j): ?>
                        <div class="journal-card">
                            <span class="j-category"><?= htmlspecialchars($j['category']) ?></span>
                            <h4><?= htmlspecialchars($j['title']) ?></h4>
                            <?php if($j['abstract']): ?>
                            <p class="j-abstract"><?= htmlspecialchars(substr($j['abstract'],0,120)) ?><?= strlen($j['abstract']) > 120 ? '…' : '' ?></p>
                            <?php endif; ?>
                            <div class="j-footer">
                                <span class="j-date"><i class="fas fa-calendar-alt" style="margin-right:4px;"></i><?= date('d M Y', strtotime($j['created_at'])) ?></span>
                                <a href="<?= htmlspecialchars($j['file_path']) ?>" target="_blank" class="btn-download">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- ═════════════════════════════════ -->


            <!-- ═══ SECTION: SUBMIT ABSTRACT ═══ -->
            <div class="member-section" id="section-submit-abstract">
                <div class="section-card" style="max-width: 720px;">
                    <div class="section-card-header">
                        <h3><i class="fas fa-file-signature" style="color:#3b82f6; margin-right:8px;"></i> Submit Research Abstract</h3>
                    </div>
                    <p style="font-size:14px; color:#64748b; margin-bottom:24px;">
                        Submit your research abstract for consideration in the JCCR Journal. Our editorial team will review your submission.
                    </p>

                    <div class="alert" id="abstractAlert"></div>

                    <form id="abstractForm" onsubmit="submitAbstract(event)">
                        <div class="form-group">
                            <label class="form-label">Research Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter the full title of your research..." required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Author(s) *</label>
                            <input type="text" name="authors" class="form-control" placeholder="e.g. John Doe, Jane Smith" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Research Category *</label>
                            <select name="category" class="form-control" required>
                                <option value="">— Select category —</option>
                                <option value="Community Development">Community Development</option>
                                <option value="Communication Research">Communication Research</option>
                                <option value="Agricultural Extension">Agricultural Extension</option>
                                <option value="Food Security">Food Security</option>
                                <option value="Rural Development">Rural Development</option>
                                <option value="Climate Change">Climate Change</option>
                                <option value="ICT in Agriculture">ICT in Agriculture</option>
                                <option value="Nutrition">Nutrition</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Abstract Text * <span style="font-size:11px; color:#94a3b8;">(250–500 words recommended)</span></label>
                            <textarea name="abstract_text" class="form-control" rows="8" placeholder="Enter your full abstract here..." required></textarea>
                        </div>
                        <button type="submit" class="btn-primary" id="btnSubmitAbstract">
                            <i class="fas fa-paper-plane"></i> Submit Abstract
                        </button>
                    </form>
                </div>
            </div>
            <!-- ═════════════════════════════════ -->


            <!-- ═══ SECTION: MY ABSTRACTS ═══ -->
            <div class="member-section" id="section-my-abstracts">
                <div class="section-card">
                    <div class="section-card-header">
                        <h3><i class="fas fa-microscope" style="color:#f59e0b; margin-right:8px;"></i> My Abstract Submissions</h3>
                        <span style="font-size:13px; color:#94a3b8;"><?= $totalAbstracts ?> submission<?= $totalAbstracts !== 1 ? 's' : '' ?></span>
                    </div>

                    <?php if(empty($myAbstracts)): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <h4>No abstracts submitted yet</h4>
                        <p>Use the "Submit Abstract" section to submit your first research abstract.</p>
                    </div>
                    <?php else: ?>
                    <div class="abstracts-grid">
                        <?php foreach($myAbstracts as $ab): ?>
                        <div class="abstract-card status-<?= $ab['status'] ?>">
                            <h5><?= htmlspecialchars($ab['title']) ?></h5>
                            <p style="font-size:12.5px; color:#94a3b8; margin-bottom:6px;">
                                <i class="fas fa-user" style="margin-right:5px;"></i><?= htmlspecialchars($ab['authors']) ?>
                            </p>
                            <p style="font-size:12px; color:#b0bec5;">
                                <?= htmlspecialchars(substr($ab['abstract_text'],0,100)) ?>…
                            </p>
                            <div class="abstract-meta">
                                <span class="status-badge status-<?= $ab['status'] ?>"><?= ucfirst($ab['status']) ?></span>
                                <span style="font-size:11px; color:#cbd5e1;"><?= date('d M Y', strtotime($ab['created_at'])) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- ═════════════════════════════════ -->


            <!-- ═══ SECTION: PROFILE ═══ -->
            <div class="member-section" id="section-profile">

                <!-- Profile Header -->
                <div class="profile-header-card">
                    <?php if(!empty($user['profile_picture'])): ?>
                    <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="<?= $initials ?>"
                         style="width:90px; height:90px; border-radius:18px; object-fit:cover; flex-shrink:0; box-shadow:0 8px 24px rgba(0,0,0,0.25); border:3px solid rgba(255,255,255,0.2);">
                    <?php else: ?>
                    <div class="profile-big-avatar"><?= $initials ?></div>
                    <?php endif; ?>
                    <div class="profile-meta">
                        <h2><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                        <p><?= htmlspecialchars($user['email']) ?></p>
                        <span class="membership-pill"><?= htmlspecialchars($user['membership_category']) ?></span>
                    </div>
                </div>

                <!-- Profile Details -->
                <div class="section-card">
                    <div class="section-card-header">
                        <h3>Profile Information</h3>
                    </div>
                    <div class="profile-fields">
                        <div class="profile-field">
                            <label>First Name</label>
                            <p><?= htmlspecialchars($user['first_name']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Last Name</label>
                            <p><?= htmlspecialchars($user['last_name']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Email Address</label>
                            <p><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Institution / Organisation</label>
                            <p><?= $user['institution'] ? htmlspecialchars($user['institution']) : '—' ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Membership Category</label>
                            <p><?= htmlspecialchars($user['membership_category']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Member Since</label>
                            <p><?= $memberSince ?></p>
                        </div>
                    </div>
                </div>

                <!-- Account Actions -->
                <div class="section-card">
                    <div class="section-card-header">
                        <h3>Account Actions</h3>
                    </div>
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <a href="/contact-us.php" target="_blank" style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:40px; background:#f1f5f9; color:#1e293b; font-size:13px; font-weight:600; text-decoration:none; border:1px solid #e2e8f0; transition:all 0.2s;">
                            <i class="fas fa-envelope" style="color:#7AD03A;"></i> Contact Support
                        </a>
                        <a href="/membership.php" target="_blank" style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:40px; background:#f1f5f9; color:#1e293b; font-size:13px; font-weight:600; text-decoration:none; border:1px solid #e2e8f0; transition:all 0.2s;">
                            <i class="fas fa-id-card" style="color:#3b82f6;"></i> Membership Info
                        </a>
                        <a href="/actions/logout.php" style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:40px; background:rgba(239,68,68,0.06); color:#ef4444; font-size:13px; font-weight:600; text-decoration:none; border:1px solid rgba(239,68,68,0.2); transition:all 0.2s;">
                            <i class="fas fa-sign-out-alt"></i> Sign Out
                        </a>
                    </div>
                </div>

            </div>
            <!-- ═════════════════════════════════ -->

        </div><!-- /.content-wrapper -->
    </div><!-- /.main-content -->

</div><!-- /.member-layout -->


<script>
// ─── SECTION NAVIGATION ───────────────────────────────────────────────────────
const sectionTitles = {
    'overview':        ['Overview',          'Dashboard'],
    'journals':        ['Research Journals', 'Publications'],
    'submit-abstract': ['Submit Abstract',   'Research'],
    'my-abstracts':    ['My Abstracts',      'Research'],
    'profile':         ['My Profile',        'Account'],
};

function switchSection(key) {
    // Hide all sections
    document.querySelectorAll('.member-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));

    const target = document.getElementById('section-' + key);
    const navTarget = document.getElementById('nav-' + key);

    if (target) target.classList.add('active');
    if (navTarget) navTarget.classList.add('active');

    const titles = sectionTitles[key] || [key, key];
    document.getElementById('topbarTitle').textContent = titles[0];
    document.getElementById('topbarSection').textContent = titles[1];

    // Close mobile sidebar
    closeSidebar();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Sidebar nav items
document.querySelectorAll('.nav-item[data-section]').forEach(el => {
    el.addEventListener('click', () => switchSection(el.dataset.section));
});

// Quick access links & overview "View All" links
document.querySelectorAll('a[data-section], .nav-item-link[data-section]').forEach(el => {
    el.addEventListener('click', (e) => {
        e.preventDefault();
        switchSection(el.dataset.section);
    });
});


// ─── MOBILE SIDEBAR TOGGLE ────────────────────────────────────────────────────
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebarOverlay');
const toggler  = document.getElementById('mobileToggle');

function openSidebar()  { sidebar.classList.add('open');  overlay.classList.add('show');  }
function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

toggler.addEventListener('click', openSidebar);
overlay.addEventListener('click', closeSidebar);


// ─── SUBMIT ABSTRACT ──────────────────────────────────────────────────────────
async function submitAbstract(e) {
    e.preventDefault();
    const alert  = document.getElementById('abstractAlert');
    const btn    = document.getElementById('btnSubmitAbstract');
    const form   = document.getElementById('abstractForm');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting…';
    alert.className = 'alert';
    alert.style.display = 'none';

    const formData = new FormData(form);

    try {
        const res  = await fetch('/actions/submit_abstract.php', { method: 'POST', body: formData });
        const data = await res.json();

        alert.className = 'alert show alert-' + (data.status === 'success' ? 'success' : 'danger');
        alert.style.display = 'block';
        alert.innerHTML = data.message;

        if (data.status === 'success') {
            form.reset();
            setTimeout(() => location.reload(), 1800);
        }
    } catch(err) {
        alert.className = 'alert show alert-danger';
        alert.style.display = 'block';
        alert.innerHTML = 'Submission failed. Please try again.';
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Abstract';
}
</script>

</body>
</html>
