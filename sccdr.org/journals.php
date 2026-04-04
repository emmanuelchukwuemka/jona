<?php 
    $pageTitle = "Research Journals";
    $pageDescription = "Explore the Journal of Community & Communication Research (JCCR) and other academic publications by SCCDR.";
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

    // Pagination logic
    $limit = 9;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $totalQuery = $pdo->query("SELECT COUNT(*) FROM journals");
    $totalRows = $totalQuery->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    $stmt = $pdo->prepare("SELECT * FROM journals ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
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
        <section class="pt-0 pb-100" style="background: #f8fafc;">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-60">
                        <h4 style="text-transform: uppercase; letter-spacing: 3px; font-size: 14px; color: var(--primary); margin-bottom: 15px;">Our Publications</h4>
                        <h2 style="font-size: 40px; color: var(--secondary); font-weight: 800;">Published Articles & Documents</h2>
                        <p style="color: var(--text-muted); font-size: 16px; max-width: 600px; margin: 15px auto 0;">
                            Browse and download research articles and documents published by SCCDR.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <?php foreach($journals as $i => $journal): ?>
                    <div class="col-lg-4 col-md-6 mb-30 wow fadeInUp" data-wow-delay="<?php echo ($i % 3) * 0.15; ?>s">
                        <div style="background: #fff; border-radius: 20px; padding: 30px; height: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.06); display: flex; flex-direction: column; transition: transform 0.3s ease, box-shadow 0.3s ease;"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 50px rgba(0,0,0,0.12)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.06)';">
                            <!-- Icon -->
                            <div style="width: 55px; height: 55px; background: rgba(122,208,58,0.1); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                <i class="fas fa-file-pdf" style="color: var(--primary); font-size: 22px;"></i>
                            </div>
                            <!-- Category badge -->
                            <span style="background: rgba(20,69,37,0.08); color: var(--secondary); padding: 4px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; display: inline-block; margin-bottom: 15px; width: fit-content;">
                                <?php echo htmlspecialchars($journal['category']); ?>
                            </span>
                            <!-- Title -->
                            <h4 style="font-size: 18px; font-weight: 700; color: var(--secondary); margin-bottom: 12px; line-height: 1.4; flex-grow: 1;">
                                <?php echo htmlspecialchars($journal['title']); ?>
                            </h4>
                            <!-- Abstract -->
                            <?php if($journal['abstract']): ?>
                            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.7; margin-bottom: 20px;">
                                <?php echo htmlspecialchars(substr($journal['abstract'], 0, 130)) . (strlen($journal['abstract']) > 130 ? '...' : ''); ?>
                            </p>
                            <?php endif; ?>
                            <!-- Footer -->
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 15px; border-top: 1px solid #f1f5f9;">
                                <span style="font-size: 12px; color: #94a3b8;">
                                    <i class="fas fa-calendar-alt mr-5"></i> <?php echo date('d M Y', strtotime($journal['created_at'])); ?>
                                </span>
                                <a href="<?php echo htmlspecialchars($journal['file_path']); ?>" target="_blank"
                                   style="background: var(--primary); color: #fff; padding: 8px 20px; border-radius: 30px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($totalPages > 1): ?>
                <div class="row mt-50">
                    <div class="col-12 text-center">
                        <ul class="pagination" style="display: inline-flex; list-style: none; padding: 0; gap: 8px;">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li>
                                <a href="?page=<?= $p ?>#feature" style="display: block; width: 40px; height: 40px; line-height: 40px; border-radius: 50%; text-align: center; text-decoration: none; font-weight: 700; transition: all 0.2s; <?php echo $p === $page ? 'background: var(--primary); color: #fff; border: 1px solid var(--primary);' : 'background: #fff; color: var(--secondary); border: 1px solid #e2e8f0;'; ?>">
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


