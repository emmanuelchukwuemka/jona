<?php
$pageTitle = "Page Not Found";
include 'includes/header.php';
?>
<section class="pt-120 pb-120 text-center" style="min-height: 70vh; display: flex; flex-direction: column; justify-content: center; align-items: center; background:#f8fafc;">
    <div style="background:rgba(122,208,58,0.1); width:120px; height:120px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:30px;">
        <i class="fas fa-exclamation-triangle" style="font-size:50px; color:#7AD03A;"></i>
    </div>
    <h1 style="font-size: 100px; font-weight: 800; color: #1e293b; margin-bottom: 20px; line-height:1;">404</h1>
    <h2 style="font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 20px;">Oops! Page Not Found</h2>
    <p style="font-size: 16px; color: #64748b; max-width: 500px; margin: 0 auto 30px; line-height:1.6;">
        The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
    </p>
    <a href="/" class="theme-btn-modern">Back to Home</a>
</section>
<?php include 'includes/footer.php'; ?>
