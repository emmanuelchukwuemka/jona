<?php
require_once 'includes/config.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: /journals.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM journals WHERE id = ?");
$stmt->execute([$id]);
$j = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$j) { header('Location: /journals.php'); exit; }

$pageTitle = htmlspecialchars($j['title']);
$pageDescription = $j['abstract'] ? htmlspecialchars(substr($j['abstract'], 0, 160)) : $pageTitle;

// Build "How to Cite" string
$authors  = $j['authors'] ?? '';
$year     = $j['year'] ?? date('Y', strtotime($j['created_at']));
$title    = $j['title'];
$volume   = $j['volume'] ?? '';
$issue    = $j['issue'] ?? '';
$doi      = $j['doi'] ?? '';
$category = $j['category'] ?? '';

$cite = '';
if ($authors) $cite .= htmlspecialchars($authors) . ' ';
if ($year)    $cite .= '(' . htmlspecialchars($year) . '). ';
$cite .= '<em>' . htmlspecialchars($title) . '</em>. ';
$cite .= 'Journal of Community &amp; Communication Research (JCCR)';
if ($volume)  $cite .= ', ' . htmlspecialchars($volume);
if ($issue)   $cite .= '(' . htmlspecialchars($issue) . ')';
if ($doi)     $cite .= '. https://doi.org/' . htmlspecialchars($doi);

include 'includes/header.php';
?>

<!-- Hero -->
<section class="hero-section" style="background: linear-gradient(135deg, #1a3a2a 0%, #2d5a3d 100%); padding: 60px 0 40px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <!-- Breadcrumb -->
                <nav style="margin-bottom:20px; font-size:13px;">
                    <a href="/" style="color:rgba(255,255,255,0.6); text-decoration:none;">Home</a>
                    <span style="color:rgba(255,255,255,0.4); margin:0 8px;">/</span>
                    <a href="/journals.php" style="color:rgba(255,255,255,0.6); text-decoration:none;">Journals</a>
                    <span style="color:rgba(255,255,255,0.4); margin:0 8px;">/</span>
                    <span style="color:rgba(255,255,255,0.9);">Article</span>
                </nav>

                <!-- Category + Volume/Issue badge row -->
                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:20px;">
                    <?php if($category): ?>
                    <span style="background:rgba(122,208,58,0.2); color:#7AD03A; padding:5px 16px; border-radius:50px; font-size:12px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase;">
                        <?= htmlspecialchars($category) ?>
                    </span>
                    <?php endif; ?>
                    <?php if($volume || $issue || $year): ?>
                    <span style="background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.85); padding:5px 16px; border-radius:50px; font-size:12px; font-weight:600;">
                        <?php
                            $parts = [];
                            if($volume) $parts[] = 'Vol. ' . htmlspecialchars($volume);
                            if($issue)  $parts[] = 'No. ' . htmlspecialchars($issue);
                            if($year)   $parts[] = htmlspecialchars($year);
                            echo implode(' &bull; ', $parts);
                        ?>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Title -->
                <h1 style="color:#fff; font-size:clamp(22px,3.5vw,36px); font-weight:800; line-height:1.35; margin-bottom:24px;">
                    <?= htmlspecialchars($j['title']) ?>
                </h1>

                <!-- Authors -->
                <?php if($j['authors']): ?>
                <p style="color:rgba(255,255,255,0.75); font-size:15px; margin-bottom:16px;">
                    <i class="fas fa-user-edit" style="margin-right:8px; color:#7AD03A;"></i>
                    <?= htmlspecialchars($j['authors']) ?>
                </p>
                <?php endif; ?>

                <!-- Published date + DOI -->
                <div style="display:flex; flex-wrap:wrap; gap:20px; font-size:13px; color:rgba(255,255,255,0.55);">
                    <span><i class="fas fa-calendar-alt" style="margin-right:5px;"></i> Published: <?= $j['published_date'] ? date('d F Y', strtotime($j['published_date'])) : date('d F Y', strtotime($j['created_at'])) ?></span>
                    <?php if($doi): ?>
                    <span><i class="fas fa-link" style="margin-right:5px;"></i> DOI: <?= htmlspecialchars($doi) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Article Body -->
<section style="background:#f8fafc; padding:60px 0 80px;">
    <div class="container">
        <div class="row">

            <!-- Main content -->
            <div class="col-lg-8 offset-lg-1">

                <!-- Abstract -->
                <?php if($j['abstract']): ?>
                <div style="background:#fff; border-radius:16px; padding:36px; margin-bottom:24px; box-shadow:0 4px 20px rgba(0,0,0,0.05); border-left:5px solid #7AD03A;">
                    <h3 style="font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#2d5a3d; margin-bottom:16px;">
                        <i class="fas fa-align-left" style="margin-right:8px; color:#7AD03A;"></i>Abstract
                    </h3>
                    <p style="font-size:15px; line-height:1.85; color:#475569; margin:0;">
                        <?= nl2br(htmlspecialchars($j['abstract'])) ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Keywords -->
                <?php if($j['keywords']): ?>
                <div style="background:#fff; border-radius:16px; padding:28px 36px; margin-bottom:24px; box-shadow:0 4px 20px rgba(0,0,0,0.05);">
                    <h3 style="font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#2d5a3d; margin-bottom:16px;">
                        <i class="fas fa-tags" style="margin-right:8px; color:#7AD03A;"></i>Keywords
                    </h3>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        <?php foreach(explode(',', $j['keywords']) as $kw): ?>
                        <span style="background:#f1f5f9; color:#475569; padding:5px 14px; border-radius:50px; font-size:13px; font-weight:500;">
                            <?= htmlspecialchars(trim($kw)) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- How to Cite -->
                <?php if($cite): ?>
                <div style="background:#fff; border-radius:16px; padding:28px 36px; margin-bottom:24px; box-shadow:0 4px 20px rgba(0,0,0,0.05);">
                    <h3 style="font-size:15px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#2d5a3d; margin-bottom:16px;">
                        <i class="fas fa-quote-right" style="margin-right:8px; color:#7AD03A;"></i>How to Cite
                    </h3>
                    <p style="font-size:14px; line-height:1.8; color:#475569; background:#f8fafc; padding:16px 20px; border-radius:10px; border:1px dashed #cbd5e1; margin:0;">
                        <?= $cite ?>
                    </p>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-3" style="margin-top:0;">
                <div style="position:sticky; top:100px;">

                    <!-- Download card -->
                    <div style="background:#fff; border-radius:16px; padding:28px; box-shadow:0 4px 20px rgba(0,0,0,0.07); margin-bottom:20px; text-align:center;">
                        <?php if($j['cover_image']): ?>
                        <img src="<?= htmlspecialchars($j['cover_image']) ?>" alt="Cover"
                             style="width:100%; max-height:180px; object-fit:cover; border-radius:10px; margin-bottom:18px;">
                        <?php else: ?>
                        <div style="width:70px; height:70px; background:rgba(239,68,68,0.08); border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 18px;">
                            <i class="fas fa-file-pdf" style="font-size:30px; color:#ef4444;"></i>
                        </div>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars($j['file_path']) ?>" target="_blank"
                           style="display:flex; align-items:center; justify-content:center; gap:8px; background:#7AD03A; color:#fff; padding:13px 20px; border-radius:10px; text-decoration:none; font-weight:700; font-size:14px; width:100%; box-sizing:border-box;">
                            <i class="fas fa-file-download"></i> Download PDF
                        </a>
                    </div>

                    <!-- Article info card -->
                    <div style="background:#fff; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.07);">
                        <h4 style="font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin-bottom:18px;">Article Info</h4>
                        <div style="display:flex; flex-direction:column; gap:14px;">
                            <?php if($volume): ?>
                            <div>
                                <div style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Volume</div>
                                <div style="font-size:14px; font-weight:600; color:#2d3e50;"><?= htmlspecialchars($volume) ?></div>
                            </div>
                            <?php endif; ?>
                            <?php if($issue): ?>
                            <div>
                                <div style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Issue</div>
                                <div style="font-size:14px; font-weight:600; color:#2d3e50;"><?= htmlspecialchars($issue) ?></div>
                            </div>
                            <?php endif; ?>
                            <?php if($year): ?>
                            <div>
                                <div style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Year</div>
                                <div style="font-size:14px; font-weight:600; color:#2d3e50;"><?= htmlspecialchars($year) ?></div>
                            </div>
                            <?php endif; ?>
                            <?php if($doi): ?>
                            <div>
                                <div style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">DOI</div>
                                <div style="font-size:13px; font-weight:500; color:#3b82f6; word-break:break-all;"><?= htmlspecialchars($doi) ?></div>
                            </div>
                            <?php endif; ?>
                            <div>
                                <div style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Published</div>
                                <div style="font-size:14px; font-weight:600; color:#2d3e50;"><?= $j['published_date'] ? date('d M Y', strtotime($j['published_date'])) : date('d M Y', strtotime($j['created_at'])) ?></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Back to Journals -->
<section style="background:#fff; padding:40px 0; border-top:1px solid #f1f5f9;">
    <div class="container text-center">
        <a href="/journals.php" style="display:inline-flex; align-items:center; gap:8px; color:#2d5a3d; font-weight:700; font-size:15px; text-decoration:none;">
            <i class="fas fa-arrow-left"></i> Back to All Journals
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
