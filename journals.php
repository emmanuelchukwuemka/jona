<?php 
    $pageTitle = "Research Journals";
    $pageDescription = "Explore the Journal of Community & Communication Research (JCCR) and other academic publications by SCCDR.";
    require_once 'includes/config.php';

    // Ensure journals table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `journals` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(500) NOT NULL,
        `authors` varchar(1000) DEFAULT NULL,
        `category` varchar(200) NOT NULL DEFAULT 'Uncategorized',
        `abstract` text DEFAULT NULL,
        `keywords` varchar(1000) DEFAULT NULL,
        `doi` varchar(300) DEFAULT NULL,
        `volume` varchar(50) DEFAULT NULL,
        `issue` varchar(50) DEFAULT NULL,
        `year` varchar(10) DEFAULT NULL,
        `file_path` varchar(500) NOT NULL,
        `cover_image` varchar(500) DEFAULT NULL,
        `uploaded_by` int(11) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Patch existing tables missing new columns
    $cols = $pdo->query("SHOW COLUMNS FROM `journals`")->fetchAll(PDO::FETCH_COLUMN);
    foreach (['authors' => "VARCHAR(1000) DEFAULT NULL AFTER `title`", 'keywords' => "VARCHAR(1000) DEFAULT NULL AFTER `abstract`", 'doi' => "VARCHAR(300) DEFAULT NULL AFTER `keywords`", 'volume' => "VARCHAR(50) DEFAULT NULL AFTER `doi`", 'issue' => "VARCHAR(50) DEFAULT NULL AFTER `volume`", 'year' => "VARCHAR(10) DEFAULT NULL AFTER `issue`", 'published_date' => "DATE DEFAULT NULL AFTER `year`", 'status' => "ENUM('published','draft') NOT NULL DEFAULT 'published' AFTER `published_date`"] as $col => $def) {
        if (!in_array($col, $cols)) $pdo->exec("ALTER TABLE `journals` ADD COLUMN `$col` $def");
    }

    // Search & filter inputs
    $search   = trim($_GET['q'] ?? '');
    $catFilter = trim($_GET['cat'] ?? '');

    // All categories for the dropdown
    $allCategories = $pdo->query("SELECT DISTINCT category FROM journals WHERE status = 'published' ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);

    // Build WHERE clause
    $where  = ["status = 'published'"];
    $params = [];
    if ($search) {
        $where[]  = "(title LIKE ? OR authors LIKE ? OR abstract LIKE ? OR keywords LIKE ?)";
        $like     = '%' . $search . '%';
        $params   = array_merge($params, [$like, $like, $like, $like]);
    }
    if ($catFilter) {
        $where[]  = "category = ?";
        $params[] = $catFilter;
    }
    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Pagination
    $limit  = 9;
    $page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM journals $whereSQL");
    $countStmt->execute($params);
    $totalRows  = $countStmt->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    $stmt = $pdo->prepare("SELECT * FROM journals $whereSQL ORDER BY created_at DESC LIMIT ? OFFSET ?");
    foreach ($params as $i => $val) $stmt->bindValue($i + 1, $val);
    $stmt->bindValue(count($params) + 1, $limit,  PDO::PARAM_INT);
    $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $journals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    include 'includes/header.php'; 
?>

      <section id="home" class="hero-section" style="background-image: url('/assets/img/slideImg2.jpeg'); background-size: cover; background-position: center;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-10 offset-lg-1 text-center">
                        <div class="hero-content-wrapper">
                            <h1 class="text-white wow fadeInDown" data-wow-delay=".2s">Research Journals</h1>
                            <div class="breadcrumb-wrapper wow fadeInUp" data-wow-delay=".4s">
                                <a href="/" style="color:var(--white); opacity: 0.8;">Home</a>
                                <span style="color:var(--white); opacity: 0.5; margin: 0 10px;">/</span>
                                <span style="color:var(--white);">Journals</span>
                            </div>
                            <div class="mt-40 wow fadeInUp" data-wow-delay=".6s">
                                <a href="/membership.php" class="theme-btn-modern">Become a Member</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!--========================= JCCR Feature Section =========================-->
        <section id="feature" class="feature-section pt-100 pb-100">
            <div class="container">
                <div class="row align-items-center mb-80">
                    <div class="col-lg-6 mb-50 mb-lg-0 text-center">
                        <div class="journal-image-wrapper wow zoomIn" data-wow-delay=".2s">
                            <img src="/assets/img/jccrImg.png" alt="JCCR Logo" class="img-fluid" style="border-radius: 25px; box-shadow: 0 30px 60px rgba(0,0,0,0.15); width: 100%; max-width: 450px; transition: var(--transition);">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="journal-content-wrapper wow fadeInRight" data-wow-delay=".4s" style="padding-left: 30px;">
                            <div class="section-modern-title text-left mb-30" style="text-align: left;">
                                <h4 style="text-transform: uppercase; letter-spacing: 3px; font-size: 14px; color: var(--primary); margin-bottom: 15px;">Academic Publications</h4>
                                <h1 style="font-size: 48px; margin-bottom: 25px;">JCCR Journal</h1>
                            </div>
                            <h3 class="mb-25" style="color: var(--secondary); font-size: 26px; font-weight: 700;">Journal of Community & Communication Research (JCCR)</h3>
                            <p class="mb-35" style="font-size: 17px; line-height: 1.8; color: var(--text-main); opacity: 0.9;">
                                The Journal of Community and Communication Research (JCCR) is a biannual open-access scholarly peer-reviewed journal
                                that publishes original and empirically based research, reviews, editorials, and research notes.
                                The JCCR is indexed by many leading services and has bright prospects for a high impact factor.
                            </p>

                            <div class="row">
                                <div class="col-md-12 mb-30">
                                    <div class="feature-box p-4 border-radius-15" style="background: rgba(122, 208, 58, 0.05); border-radius: 15px; border-left: 5px solid var(--primary);">
                                        <h5 class="mb-10" style="font-size: 20px; color: var(--secondary);"><i class="fas fa-check-circle mr-10" style="color: var(--primary);"></i> Scope of Focus</h5>
                                        <p style="font-size: 15px; color: var(--text-muted);">Agriculture, Agricultural Extension, Rural Development, Communication, Education, Nutrition, Food Security, Climate Change, and ICT-in-Agriculture.</p>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-30">
                                    <div class="feature-box p-4 border-radius-15" style="background: rgba(58, 66, 78, 0.05); border-radius: 15px; border-left: 5px solid var(--secondary);">
                                        <h5 class="mb-10" style="font-size: 20px; color: var(--secondary);"><i class="fas fa-calendar-alt mr-10" style="color: var(--primary);"></i> Publication Cycle</h5>
                                        <p style="font-size: 15px; color: var(--text-muted);">Published bi-annually in the months of June and December.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-60 text-center">
                    <div class="col-lg-4 col-md-6 mb-30">
                        <a href="/membership.php" class="theme-btn-modern w-100 wow fadeInUp" data-wow-delay=".2s">
                            <i class="fas fa-user-plus mr-10"></i> Become a Member
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-30">
                        <a href="tel:+2348060790069" class="theme-btn-modern w-100 wow fadeInUp" data-wow-delay=".4s" style="background: var(--secondary);">
                            <i class="fas fa-phone-alt mr-10"></i> Call JCCR
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-30">
                        <a href="mailto:infojccr@gmail.com" class="theme-btn-modern w-100 wow fadeInUp" data-wow-delay=".6s" style="background: var(--secondary);">
                            <i class="fas fa-envelope mr-10"></i> Email JCCR
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!--========================= Published Articles Section =========================-->
        <?php if (!empty($journals)): ?>
        <section class="pt-60 pb-100" style="background: #f8fafc;">
            <div class="container">

                <div class="row mb-40">
                    <div class="col-12 text-center">
                        <h4 style="text-transform:uppercase; letter-spacing:3px; font-size:14px; color:var(--primary); margin-bottom:15px;">Our Publications</h4>
                        <h2 style="font-size:38px; color:var(--secondary); font-weight:800;">Published Articles</h2>
                        <p style="color:var(--text-muted); font-size:16px; max-width:600px; margin:15px auto 0;">
                            Browse research articles and documents published by SCCDR.
                        </p>
                    </div>
                </div>

                <!-- Search & Filter Bar -->
                <form method="GET" action="/journals.php" style="margin-bottom:40px;">
                    <div style="background:#fff; border-radius:14px; padding:20px 24px; box-shadow:0 4px 20px rgba(0,0,0,0.06); display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end;">

                        <!-- Search input -->
                        <div style="flex:1; min-width:220px;">
                            <label style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#94a3b8; display:block; margin-bottom:7px;">Search</label>
                            <div style="position:relative;">
                                <i class="fas fa-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:13px;"></i>
                                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                                       placeholder="Title, author, keyword…"
                                       style="width:100%; padding:11px 14px 11px 38px; border:1px solid #e2e8f0; border-radius:9px; font-size:14px; font-family:inherit; outline:none; color:#334155; box-sizing:border-box;"
                                       onfocus="this.style.borderColor='#7AD03A'" onblur="this.style.borderColor='#e2e8f0'">
                            </div>
                        </div>

                        <!-- Category filter -->
                        <div style="min-width:200px;">
                            <label style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#94a3b8; display:block; margin-bottom:7px;">Category</label>
                            <select name="cat" style="width:100%; padding:11px 14px; border:1px solid #e2e8f0; border-radius:9px; font-size:14px; font-family:inherit; outline:none; color:#334155; background:#fff; cursor:pointer;"
                                    onfocus="this.style.borderColor='#7AD03A'" onblur="this.style.borderColor='#e2e8f0'">
                                <option value="">All Categories</option>
                                <?php foreach($allCategories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" <?= $catFilter === $cat ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                                style="padding:11px 28px; background:#1e3a2f; color:#fff; border:none; border-radius:9px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; white-space:nowrap;">
                            <i class="fas fa-filter" style="margin-right:7px;"></i>Filter
                        </button>

                        <?php if($search || $catFilter): ?>
                        <a href="/journals.php"
                           style="padding:11px 20px; border:1px solid #e2e8f0; border-radius:9px; font-size:13px; font-weight:600; color:#64748b; text-decoration:none; white-space:nowrap;">
                            <i class="fas fa-times" style="margin-right:5px;"></i>Clear
                        </a>
                        <?php endif; ?>

                    </div>

                    <?php if($search || $catFilter): ?>
                    <div style="margin-top:12px; font-size:13px; color:#64748b; padding-left:4px;">
                        <?= $totalRows ?> result<?= $totalRows !== 1 ? 's' : '' ?> found
                        <?php if($search): ?> for <strong>"<?= htmlspecialchars($search) ?>"</strong><?php endif; ?>
                        <?php if($catFilter): ?> in <strong><?= htmlspecialchars($catFilter) ?></strong><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </form>

                <!-- Article list -->
                <div style="display:flex; flex-direction:column; gap:20px;">
                    <?php foreach($journals as $i => $journal): ?>
                    <div style="background:#fff; border-radius:16px; padding:30px 36px; box-shadow:0 4px 20px rgba(0,0,0,0.05); border:1px solid #e8edf2; display:flex; gap:28px; align-items:flex-start; transition:box-shadow 0.2s, transform 0.2s;"
                         onmouseover="this.style.boxShadow='0 12px 40px rgba(0,0,0,0.10)'; this.style.transform='translateY(-2px)'"
                         onmouseout="this.style.boxShadow='0 4px 20px rgba(0,0,0,0.05)'; this.style.transform=''">

                        <!-- Cover thumbnail -->
                        <?php if(!empty($journal['cover_image'])): ?>
                        <div style="flex-shrink:0; width:80px; height:110px; border-radius:8px; overflow:hidden; border:1px solid #e8edf2;">
                            <img src="<?= htmlspecialchars($journal['cover_image']) ?>" alt="Cover"
                                 style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <?php else: ?>
                        <div style="flex-shrink:0; width:60px; height:60px; background:rgba(239,68,68,0.07); border-radius:12px; display:flex; align-items:center; justify-content:center; margin-top:4px;">
                            <i class="fas fa-file-pdf" style="font-size:24px; color:#ef4444;"></i>
                        </div>
                        <?php endif; ?>

                        <!-- Content -->
                        <div style="flex:1; min-width:0;">

                            <!-- Badges -->
                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
                                <?php if($journal['category']): ?>
                                <span style="background:rgba(122,208,58,0.1); color:#2d5a3d; padding:3px 12px; border-radius:50px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                                    <?= htmlspecialchars($journal['category']) ?>
                                </span>
                                <?php endif; ?>
                                <?php if($journal['volume'] || $journal['issue'] || $journal['year']): ?>
                                <span style="background:#f1f5f9; color:#64748b; padding:3px 12px; border-radius:50px; font-size:11px; font-weight:600;">
                                    <?php
                                        $parts = [];
                                        if($journal['volume']) $parts[] = 'Vol. ' . htmlspecialchars($journal['volume']);
                                        if($journal['issue'])  $parts[] = 'No. '  . htmlspecialchars($journal['issue']);
                                        if($journal['year'])   $parts[] = htmlspecialchars($journal['year']);
                                        echo implode(' &bull; ', $parts);
                                    ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <!-- Title -->
                            <h3 style="font-size:18px; font-weight:700; color:#1e3a2f; margin-bottom:8px; line-height:1.45;">
                                <a href="/journal-article.php?id=<?= $journal['id'] ?>"
                                   style="color:inherit; text-decoration:none;"
                                   onmouseover="this.style.color='#7AD03A'" onmouseout="this.style.color='#1e3a2f'">
                                    <?= htmlspecialchars($journal['title']) ?>
                                </a>
                            </h3>

                            <!-- Authors -->
                            <?php if($journal['authors']): ?>
                            <p style="font-size:13px; color:#64748b; margin-bottom:10px;">
                                <i class="fas fa-user-edit" style="margin-right:5px; color:#7AD03A;"></i>
                                <?= htmlspecialchars($journal['authors']) ?>
                            </p>
                            <?php endif; ?>

                            <!-- Abstract snippet -->
                            <?php if($journal['abstract']): ?>
                            <p style="font-size:14px; color:#64748b; line-height:1.7; margin-bottom:14px;">
                                <?= htmlspecialchars(substr($journal['abstract'], 0, 200)) ?><?= strlen($journal['abstract']) > 200 ? '…' : '' ?>
                            </p>
                            <?php endif; ?>

                            <!-- Keywords -->
                            <?php if($journal['keywords']): ?>
                            <div style="margin-bottom:14px; display:flex; flex-wrap:wrap; gap:6px;">
                                <?php foreach(array_slice(explode(',', $journal['keywords']), 0, 5) as $kw): ?>
                                <span style="background:#f8fafc; border:1px solid #e2e8f0; color:#475569; padding:2px 10px; border-radius:50px; font-size:11px;">
                                    <?= htmlspecialchars(trim($kw)) ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Footer row -->
                            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding-top:14px; border-top:1px solid #f1f5f9;">
                                <span style="font-size:12px; color:#94a3b8;">
                                    <i class="fas fa-calendar-alt" style="margin-right:4px;"></i>
                                    <?= $journal['published_date'] ? date('d M Y', strtotime($journal['published_date'])) : date('d M Y', strtotime($journal['created_at'])) ?>
                                    <?php if($journal['doi']): ?>
                                    &nbsp;&bull;&nbsp; <i class="fas fa-link" style="margin-right:4px;"></i> <?= htmlspecialchars($journal['doi']) ?>
                                    <?php endif; ?>
                                </span>
                                <div style="display:flex; gap:10px;">
                                    <a href="/journal-article.php?id=<?= $journal['id'] ?>"
                                       style="display:inline-flex; align-items:center; gap:6px; padding:8px 18px; border-radius:8px; border:1px solid #7AD03A; color:#2d5a3d; font-size:13px; font-weight:700; text-decoration:none; transition:all 0.2s;"
                                       onmouseover="this.style.background='#7AD03A'; this.style.color='#fff'"
                                       onmouseout="this.style.background=''; this.style.color='#2d5a3d'">
                                        <i class="fas fa-eye"></i> View Article
                                    </a>
                                    <a href="<?= htmlspecialchars($journal['file_path']) ?>" target="_blank"
                                       style="display:inline-flex; align-items:center; gap:6px; padding:8px 18px; border-radius:8px; background:#7AD03A; color:#fff; font-size:13px; font-weight:700; text-decoration:none;">
                                        <i class="fas fa-download"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="row mt-50">
                    <div class="col-12 text-center">
                        <ul class="pagination" style="display:inline-flex; list-style:none; padding:0; gap:8px;">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li>
                                <?php
                            $qs = http_build_query(array_filter(['q' => $search, 'cat' => $catFilter, 'page' => $p]));
                        ?>
                        <a href="?<?= $qs ?>" style="display:block; width:40px; height:40px; line-height:40px; border-radius:50%; text-align:center; text-decoration:none; font-weight:700; transition:all 0.2s; <?= $p === $page ? 'background:var(--primary); color:#fff; border:1px solid var(--primary);' : 'background:#fff; color:var(--secondary); border:1px solid #e2e8f0;' ?>">
                            <?= $p ?>
                        </a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </section>
        <?php endif; ?>
     
<?php include 'includes/footer.php'; ?>


