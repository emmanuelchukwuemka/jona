<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content Area -->
<main class="main-content">
    <!-- Header Bar -->
    <header class="topbar">
        <div class="page-title">
            <h2 id="current-section-title">Dashboard</h2>
        </div>
        <div class="top-actions">
            <div class="user-chip" style="font-weight: 600; font-size: 14px;">
                <span>Welcome, Admin</span>
            </div>
        </div>
    </header>

    <!-- Main Content Sections -->
    <div class="content-wrapper">
        <?php 
            include 'includes/sections/dashboard.php';
            include 'includes/sections/messages.php';
            include 'includes/sections/journals.php';
            include 'includes/sections/abstracts.php';
            include 'includes/sections/blog.php';
            include 'includes/sections/newsletters.php';
            include 'includes/sections/resources.php';
            include 'includes/sections/events.php';
            include 'includes/sections/site_settings.php';
            include 'includes/sections/media_library.php';
            include 'includes/sections/board_members.php';
            include 'includes/sections/users.php';
            include 'includes/sections/payments.php';
        ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

