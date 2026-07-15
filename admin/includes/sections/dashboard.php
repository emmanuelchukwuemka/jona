<?php
require_once __DIR__ . '/../../../includes/config.php';

// Real counts from DB
$totalJournals  = (int) $pdo->query("SELECT COUNT(*) FROM journals")->fetchColumn();
$totalPosts     = 0;
try { $totalPosts = (int) $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn(); } catch(PDOException $e) {}
$totalMembers   = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'member'")->fetchColumn();
$totalAbstracts = 0;
try { $totalAbstracts = (int) $pdo->query("SELECT COUNT(*) FROM abstracts")->fetchColumn(); } catch(PDOException $e) {}
$unreadMessages = 0;
try { $unreadMessages = (int) $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn(); } catch(PDOException $e) {}
$pendingAbstracts = 0;
try { $pendingAbstracts = (int) $pdo->query("SELECT COUNT(*) FROM abstracts WHERE status = 'submitted'")->fetchColumn(); } catch(PDOException $e) {}
?>

<!-- Dashboard Overview -->
<div id="section-dashboard" class="admin-section">
    <div class="dashboard-grid">
        <div class="stats-card">
            <div class="stats-icon"><i class="fas fa-book"></i></div>
            <div class="stats-info">
                <h3><?= $totalJournals ?></h3>
                <p>Journals Published</p>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon" style="background:rgba(59,130,246,0.12); color:#3b82f6;"><i class="fas fa-newspaper"></i></div>
            <div class="stats-info">
                <h3><?= $totalPosts ?></h3>
                <p>Blog Posts</p>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon" style="background:rgba(139,92,246,0.12); color:#8b5cf6;"><i class="fas fa-user-friends"></i></div>
            <div class="stats-info">
                <h3><?= $totalMembers ?></h3>
                <p>Registered Members</p>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon" style="background:rgba(245,158,11,0.12); color:#f59e0b;"><i class="fas fa-microscope"></i></div>
            <div class="stats-info">
                <h3><?= $totalAbstracts ?></h3>
                <p>Abstracts Submitted</p>
            </div>
        </div>
    </div>

    <!-- Secondary row -->
    <div class="dashboard-grid" style="margin-top:0;">
        <div class="stats-card">
            <div class="stats-icon" style="background:rgba(239,68,68,0.1); color:#ef4444;"><i class="fas fa-inbox"></i></div>
            <div class="stats-info">
                <h3><?= $unreadMessages ?></h3>
                <p>Unread Messages</p>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon" style="background:rgba(20,184,166,0.12); color:#14b8a6;"><i class="fas fa-hourglass-half"></i></div>
            <div class="stats-info">
                <h3><?= $pendingAbstracts ?></h3>
                <p>Abstracts Pending Review</p>
            </div>
        </div>
        <div class="stats-card" style="cursor:default; opacity:0.5;">
            <div class="stats-icon" style="background:#f1f5f9; color:#94a3b8;"><i class="fas fa-calendar-alt"></i></div>
            <div class="stats-info">
                <h3 style="font-size:14px; color:#94a3b8;">—</h3>
                <p>Events (coming soon)</p>
            </div>
        </div>
        <div class="stats-card" style="cursor:default; opacity:0.5;">
            <div class="stats-icon" style="background:#f1f5f9; color:#94a3b8;"><i class="fas fa-envelope-open-text"></i></div>
            <div class="stats-info">
                <h3 style="font-size:14px; color:#94a3b8;">—</h3>
                <p>Subscribers (coming soon)</p>
            </div>
        </div>
    </div>
</div>
