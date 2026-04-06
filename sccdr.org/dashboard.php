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

            <a class="nav-item" data-section="inquiries" id="nav-inquiries">
                <i class="fas fa-comment-medical"></i>
                <span>My Inquiries</span>
                <?php 
                    $unreadReplies = $pdo->prepare("SELECT COUNT(*) FROM messages m JOIN message_replies r ON m.id = r.message_id WHERE m.user_id = ? AND m.status = 'replied'");
                    $unreadReplies->execute([$_SESSION['user_id']]);
                    $replyCount = $unreadReplies->fetchColumn();
                    if($replyCount > 0): 
                ?>
                <span class="badge" style="background:#7AD03A;"><?= $replyCount ?></span>
                <?php endif; ?>
            </a>

            <div class="nav-label">My Account</div>

            <a class="nav-item" data-section="profile" id="nav-profile">
                <i class="fas fa-user-circle"></i>
                <span>My Profile</span>
            </a>

            <a class="nav-item" data-section="billing" id="nav-billing">
                <i class="fas fa-credit-card"></i>
                <span>Billing & Membership</span>
                <?php if($user['subscription_status'] !== 'active'): ?>
                <span class="badge" style="background:#ef4444;">Unpaid</span>
                <?php endif; ?>
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
                        <a href="#" onclick="openEditProfileModal()" style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:40px; background:#f1f5f9; color:#1e293b; font-size:13px; font-weight:600; text-decoration:none; border:1px solid #e2e8f0; transition:all 0.2s;">
                            <i class="fas fa-user-edit" style="color:#8b5cf6;"></i> Edit Profile
                        </a>
                        <a href="#" onclick="openChangePasswordModal()" style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:40px; background:#f1f5f9; color:#1e293b; font-size:13px; font-weight:600; text-decoration:none; border:1px solid #e2e8f0; transition:all 0.2s;">
                            <i class="fas fa-key" style="color:#f59e0b;"></i> Change Password
                        </a>
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


            <!-- ═══ SECTION: BILLING & MEMBERSHIP ═══ -->
            <div class="member-section" id="section-billing">
                <div class="section-card">
                    <div class="section-card-header">
                        <h3><i class="fas fa-credit-card" style="color:#10b981; margin-right:8px;"></i> Billing & Membership</h3>
                    </div>

                    <!-- Current Status Card -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Subscription Status</div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <h3 style="margin: 0; font-size: 24px; font-weight: 800; color: #1e293b;">
                                    <?= ucfirst($user['subscription_status']) ?>
                                </h3>
                                <span class="status-badge status-<?= $user['subscription_status'] === 'active' ? 'accepted' : 'rejected' ?>" style="padding: 4px 12px; border-radius: 20px; font-weight: 700;">
                                    <?= $user['subscription_status'] === 'active' ? 'Active' : 'Payment Required' ?>
                                </span>
                            </div>
                            <?php if($user['subscription_end']): ?>
                            <p style="margin: 8px 0 0; color: #64748b; font-size: 14px;">Next renewal: <?= date('d M Y', strtotime($user['subscription_end'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if($user['subscription_status'] === 'active'): ?>
                        <div style="text-align: right;">
                            <div style="font-size: 13px; color: #10b981; font-weight: 700; margin-bottom: 5px;"><i class="fas fa-check-circle"></i> Fully Paid</div>
                            <div style="font-size: 12px; color: #94a3b8;">Membership tier: <?= htmlspecialchars($user['membership_category']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if($user['subscription_status'] !== 'active'): ?>
                    <h4 style="margin-bottom: 20px; font-weight: 700; color: #1e293b;">Complete Your Registration</h4>
                    <p style="color: #64748b; margin-bottom: 24px;">To access full member benefits including journal publishing and abstract submissions, please complete your membership payment for the <strong><?= htmlspecialchars($user['membership_category']) ?></strong> tier.</p>

                    <div style="background: #fff; border: 2px solid #7AD03A; border-radius: 16px; padding: 30px; text-align: center; box-shadow: 0 10px 30px rgba(122,208,58,0.1);">
                        <div style="width: 60px; height: 60px; background: rgba(122,208,58,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-crown" style="font-size: 28px; color: #7AD03A;"></i>
                        </div>
                        <h2 style="font-size: 32px; font-weight: 800; color: #1e293b; margin-bottom: 5px;">
                            <?php 
                                $price = $membership_prices[$user['membership_category']]['amount'] ?? 0;
                                echo '$' . number_format($price, 2);
                            ?>
                        </h2>
                        <p style="color: #64748b; font-weight: 600; margin-bottom: 25px;">Per Year</p>
                        
                        <ul style="text-align: left; max-width: 300px; margin: 0 auto 30px; list-style: none; padding: 0;">
                            <li style="margin-bottom: 12px; font-size: 14px; color: #475569;"><i class="fas fa-check-circle" style="color: #7AD03A; margin-right: 10px;"></i> Full Research Access</li>
                            <li style="margin-bottom: 12px; font-size: 14px; color: #475569;"><i class="fas fa-check-circle" style="color: #7AD03A; margin-right: 10px;"></i> Conference Abstract Submission</li>
                            <li style="margin-bottom: 12px; font-size: 14px; color: #475569;"><i class="fas fa-check-circle" style="color: #7AD03A; margin-right: 10px;"></i> Voting Rights (FSCCDR)</li>
                            <li style="margin-bottom: 12px; font-size: 14px; color: #475569;"><i class="fas fa-check-circle" style="color: #7AD03A; margin-right: 10px;"></i> Digital Member ID Card</li>
                        </ul>

                        <button onclick="startPaymentFlow()" id="btnPayNow" class="btn-primary" style="width: 100%; max-width: 300px; padding: 18px; font-size: 16px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; background: linear-gradient(135deg, #7AD03A 0%, #144525 100%);">
                            Pay Now <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                        </button>
                        <p style="margin-top: 15px; font-size: 12px; color: #94a3b8;"><i class="fas fa-lock"></i> Secure Payment by Stripe</p>
                    </div>
                    <?php else: ?>
                    <div style="background: #ecfdf5; border: 1px solid #10b981; border-radius: 12px; padding: 24px; text-align: center;">
                        <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981; margin-bottom: 15px;"></i>
                        <h4 style="color: #065f46; font-weight: 700; margin-bottom: 10px;">You are fully subscribed</h4>
                        <p style="color: #047857; margin: 0;">Thank you for being a valued member of SCCDR. Your benefits are fully active.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- ═════════════════════════════════ -->

            <!-- ═══ SECTION: SUPPORT DESK (CHATS) ═══ -->
            <div class="member-section" id="section-inquiries">
                <div class="section-card">
                    <div class="section-card-header d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="mb-1"><i class="fas fa-comment-medical text-emerald me-2"></i> Support Desk</h3>
                            <p style="font-size: 13px; color: #64748b; margin: 0;">Direct professional correspondence with SCCDR Administration.</p>
                        </div>
                        <button class="btn-emerald-outline" onclick="openNewInquiryModal()" style="padding: 10px 20px; font-size: 13px; font-weight: 700; border-radius: 12px;">
                            <i class="fas fa-plus me-1"></i> Start New Inquiry
                        </button>
                    </div>

                    <?php 
                    $stmtI = $pdo->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY created_at DESC");
                    $stmtI->execute([$_SESSION['user_id']]);
                    $myInquiries = $stmtI->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if(empty($myInquiries)): ?>
                        <div class="empty-state text-center py-5">
                            <div style="width: 80px; height: 80px; background: rgba(34, 197, 94, 0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="fas fa-comments-alt" style="font-size: 32px; color: #22c55e; opacity: 0.4;"></i>
                            </div>
                            <h4>No active conversations</h4>
                            <p style="color: #64748b;">Our support team is here to help. Start a new professional inquiry above.</p>
                        </div>
                    <?php else: ?>
                        <div class="support-chat-container">
                            <?php foreach($myInquiries as $inq): ?>
                                <div class="support-chat-thread mb-4" id="thread-<?= $inq['id'] ?>">
                                    <div class="thread-header d-flex justify-content-between align-items-center p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                                        <div>
                                            <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px;">Thread #<?= $inq['id'] ?> &bull; Initiated <?= date('d M, Y', strtotime($inq['created_at'])) ?></span>
                                            <div style="font-weight: 700; color: #1e293b; font-size: 14px;"><?= htmlspecialchars(substr($inq['message'], 0, 60)) . (strlen($inq['message']) > 60 ? '...' : '') ?></div>
                                        </div>
                                        <span class="status-badge status-<?= $inq['status'] === 'replied' ? 'accepted' : 'review' ?>" style="font-size: 10px; padding: 4px 12px; border-radius: 20px;">
                                            <?= $inq['status'] === 'replied' ? 'Response Received' : 'Under Review' ?>
                                        </span>
                                    </div>
                                    <div class="thread-messages p-4" style="background: white; border: 1px solid #e2e8f0; border-top: none; max-height: 450px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px;">
                                        
                                        <!-- Original Member Inquiry -->
                                        <div class="chat-bubble bubble-member">
                                            <div class="bubble-meta">You &bull; <?= date('d M, g:i A', strtotime($inq['created_at'])) ?></div>
                                            <div class="bubble-text"><?= nl2br(htmlspecialchars($inq['message'])) ?></div>
                                        </div>

                                        <?php 
                                        $stmtR = $pdo->prepare("SELECT * FROM message_replies WHERE message_id = ? ORDER BY created_at ASC");
                                        $stmtR->execute([$inq['id']]);
                                        $replies = $stmtR->fetchAll(PDO::FETCH_ASSOC);
                                        ?>

                                        <?php foreach($replies as $reply): ?>
                                            <div class="chat-bubble bubble-<?= $reply['sender_type'] ?>">
                                                <div class="bubble-meta"><?= $reply['sender_type'] === 'admin' ? 'SCCDR Official' : 'You' ?> &bull; <?= date('d M, g:i A', strtotime($reply['created_at'])) ?></div>
                                                <div class="bubble-text"><?= nl2br(htmlspecialchars($reply['reply_text'])) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="thread-footer p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-top: none; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                        <div class="d-flex gap-2">
                                            <input type="text" id="chat-reply-input-<?= $inq['id'] ?>" class="form-control" placeholder="Type your professional response..." style="border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: 14px; padding: 10px 15px;">
                                            <button class="btn-emerald-solid" style="padding: 10px 25px; font-size: 13px;" onclick="sendDashboardChat(<?= $inq['id'] ?>)">
                                                Send <i class="fas fa-paper-plane ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

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
    'billing':         ['Billing & Membership', 'Subscription'],
    'profile':         ['My Profile',        'Account'],
    'inquiries':       ['My Inquiries',      'Correspondence'],
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

// ─── BILLING & PAYMENTS ───────────────────────────────────────────────────────
async function startPaymentFlow() {
    const btn = document.getElementById('btnPayNow');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    try {
        const response = await fetch('/actions/create_checkout_session.php', {
            method: 'POST'
        });
        const data = await response.json();
        
        if (data.url) {
            window.location.href = data.url;
        } else {
            alert(data.error || 'Failed to initialize payment session.');
            btn.disabled = false;
            btn.innerHTML = 'Pay Now <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>';
        }
    } catch (error) {
        console.error('Payment Error:', error);
        alert('An unexpected error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = 'Pay Now <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>';
    }
}

// ─── EDIT PROFILE MODAL ────────────────────────────────────────────────────────
function openEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'flex';
}
function closeEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'none';
}
async function submitEditProfile(e) {
    e.preventDefault();
    const btn = document.getElementById('btnEditProfile');
    const alert = document.getElementById('epAlert');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    alert.style.display = 'none';

    try {
        const res = await fetch('/actions/update_profile.php', { method: 'POST', body: new FormData(e.target) });
        const data = await res.json();
        alert.className = 'alert show alert-' + (data.status === 'success' ? 'success' : 'danger');
        alert.style.display = 'block';
        alert.innerHTML = data.message;
        if (data.status === 'success') setTimeout(() => location.reload(), 1000);
    } catch(err) {
        alert.className = 'alert show alert-danger';
        alert.style.display = 'block';
        alert.innerHTML = 'An network error occurred.';
    }
    btn.disabled = false; btn.innerHTML = 'Save Changes';
}

// ─── CHANGE PASSWORD MODAL ─────────────────────────────────────────────────────
function openChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'flex';
}
function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'none';
}
async function submitChangePassword(e) {
    e.preventDefault();
    if (document.getElementById('cpNew').value !== document.getElementById('cpConfirm').value) {
        const alert = document.getElementById('cpAlert');
        alert.className = 'alert show alert-danger';
        alert.style.display = 'block';
        alert.innerHTML = 'New passwords do not match.';
        return;
    }
    const btn = document.getElementById('btnChangePassword');
    const alert = document.getElementById('cpAlert');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    alert.style.display = 'none';

    try {
        const res = await fetch('/actions/change_password.php', { method: 'POST', body: new FormData(e.target) });
        const data = await res.json();
        alert.className = 'alert show alert-' + (data.status === 'success' ? 'success' : 'danger');
        alert.style.display = 'block';
        alert.innerHTML = data.message;
        if (data.status === 'success') {
            e.target.reset();
            setTimeout(closeChangePasswordModal, 1500);
        }
    } catch(err) {
        alert.className = 'alert show alert-danger';
        alert.style.display = 'block';
        alert.innerHTML = 'An network error occurred.';
    }
    btn.disabled = false; btn.innerHTML = 'Change Password';
}
</script>

<!-- MODALS -->
<div id="newInquiryModal" class="sidebar-overlay" style="display:none; align-items:center; justify-content:center; padding:20px; z-index:9999;">
    <div style="background:#fff; border-radius:16px; padding:30px; width:100%; max-width:500px; box-shadow:0 20px 60px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 style="margin:0; font-weight:800; color:#081e0f;">Start New Inquiry</h3>
            <button onclick="closeNewInquiryModal()" style="background:none; border:none; color:#94a3b8; cursor:pointer; font-size:20px;"><i class="fas fa-times"></i></button>
        </div>
        <p style="font-size:14px; color:#64748b; margin-bottom:24px;">Our administration team typically responds within 24–48 professional hours. Please describe your inquiry clearly.</p>
        
        <div id="niAlert" class="alert d-none"></div>
        
        <form onsubmit="submitNewInquiry(event)">
            <div class="form-group mb-4">
                <label class="form-label" style="font-weight:700; color:#1e293b; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">Your Message *</label>
                <textarea id="niMessage" class="form-control" rows="5" placeholder="Enter your detailed inquiry here..." required style="border-radius:12px; border:1.5px solid #e2e8f0; padding:15px;"></textarea>
            </div>
            <div style="display:flex; gap:12px;">
                <button type="button" onclick="closeNewInquiryModal()" class="btn-emerald-outline" style="flex:1; padding:14px; border-radius:12px;">Cancel</button>
                <button type="submit" id="btnSubmitNI" class="btn-emerald-solid" style="flex:2; padding:12px; border-radius:12px; font-weight:800; font-size:14px;">
                    Send Message <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="editProfileModal" class="sidebar-overlay" style="display:none; align-items:center; justify-content:center; padding:20px; z-index:9999;" class="show">
    <div style="background:#fff; border-radius:16px; padding:24px; width:100%; max-width:400px; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom:16px;">Edit Profile</h3>
        <div id="epAlert" class="alert"></div>
        <form onsubmit="submitEditProfile(event)">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Institution / Organisation</label>
                <input type="text" name="institution" class="form-control" value="<?= htmlspecialchars($user['institution'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Profile Picture (Optional)</label>
                <input type="file" name="profile_picture" class="form-control" accept="image/*">
            </div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeEditProfileModal()" class="btn-primary" style="background:#f1f5f9; color:#475569; flex:1;">Cancel</button>
                <button type="submit" id="btnEditProfile" class="btn-primary" style="flex:1;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="changePasswordModal" class="sidebar-overlay" style="display:none; align-items:center; justify-content:center; padding:20px; z-index:9999;" class="show">
    <div style="background:#fff; border-radius:16px; padding:24px; width:100%; max-width:400px; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom:16px;">Change Password</h3>
        <div id="cpAlert" class="alert"></div>
        <form onsubmit="submitChangePassword(event)">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" id="cpNew" name="new_password" class="form-control" minlength="6" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" id="cpConfirm" name="confirm_password" class="form-control" minlength="6" required>
            </div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeChangePasswordModal()" class="btn-primary" style="background:#f1f5f9; color:#475569; flex:1;">Cancel</button>
                <button type="submit" id="btnChangePassword" class="btn-primary" style="flex:1; background:#f59e0b;">Change Password</button>
            </div>
        </form>
    </div>
</div>


<script>
// ─── CHAT SYSTEM ──────────────────────────────────────────────────────────────
async function sendDashboardChat(messageId) {
    const input = document.getElementById('chat-reply-input-' + messageId);
    const replyText = input.value.trim();
    if(!replyText) return;

    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    const formData = new FormData();
    formData.append('message_id', messageId);
    formData.append('reply_text', replyText);

    try {
        const res = await fetch('/actions/send_chat_message.php', { method: 'POST', body: formData });
        const data = await res.json();
        if(data.status === 'success') {
            location.reload(); // Refresh to show new message
        } else {
            alert(data.message);
        }
    } catch(e) {
        alert('Connection error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

function openNewInquiryModal() {
    document.getElementById('newInquiryModal').style.display = 'flex';
    setTimeout(() => document.getElementById('niMessage').focus(), 100);
}

function closeNewInquiryModal() {
    document.getElementById('newInquiryModal').style.display = 'none';
    document.getElementById('niMessage').value = '';
    document.getElementById('niAlert').classList.add('d-none');
}

async function submitNewInquiry(e) {
    e.preventDefault();
    const msg = document.getElementById('niMessage').value.trim();
    const alertBox = document.getElementById('niAlert');
    const btn = document.getElementById('btnSubmitNI');
    
    if(!msg) return;

    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i> Processing...';

    const formData = new FormData();
    formData.append('reply_text', msg);
    formData.append('is_new_thread', 1);

    try {
        const res = await fetch('/actions/send_chat_message.php', { method: 'POST', body: formData });
        const data = await res.json();
        
        if(data.status === 'success') {
            alertBox.className = 'alert alert-success mb-4';
            alertBox.innerHTML = '<i class="fas fa-check-circle me-2"></i> Conversation thread initialized. Redirecting...';
            alertBox.classList.remove('d-none');
            setTimeout(() => location.reload(), 1200);
        } else {
            throw new Error(data.message);
        }
    } catch(e) {
        alertBox.className = 'alert alert-danger mb-4';
        alertBox.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> ' + e.message;
        alertBox.classList.remove('d-none');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}
</script>

<style>
/* Support Desk Chat Aesthetics */
.support-chat-container { border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; gap: 20px; }
.support-chat-thread { border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
.thread-messages { display: flex; flex-direction: column; gap: 12px; }
.chat-bubble { max-width: 85%; padding: 12px 18px; border-radius: 18px; position: relative; display: flex; flex-direction: column; }
.bubble-meta { font-size: 10px; font-weight: 700; color: #94a3b8; margin-bottom: 4px; }
.bubble-text { font-size: 13.5px; line-height: 1.55; }

.bubble-member { align-self: flex-start; background: #f8fafc; border: 1px solid #e2e8f0; border-bottom-left-radius: 4px; }
.bubble-member .bubble-text { color: #334155; }

.bubble-admin { align-self: flex-end; background: #081e0f; color: #fff; border-bottom-right-radius: 4px; box-shadow: 0 4px 12px rgba(8,30,15,0.15); }
.bubble-admin .bubble-text { color: #fff; }
.bubble-admin .bubble-meta { color: #22c55e; }

.text-emerald { color: #22c55e !important; }
.btn-emerald-solid { background: #22c55e; color: white; border: none; border-radius: 8px; transition: all 0.2s; font-weight: 700; }
.btn-emerald-solid:hover { background: #16a34a; transform: scale(1.02); }
.btn-emerald-outline { background: transparent; border: 1.5px solid #22c55e; color: #22c55e; transition: all 0.2s; }
.btn-emerald-outline:hover { background: #f0fdf4; }
</style>

</body>
</html>
