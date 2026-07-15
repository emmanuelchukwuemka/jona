<?php
require_once __DIR__ . '/../../../includes/config.php';

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

$journals = $pdo->query("SELECT * FROM journals ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Group by category for stats
$byCategory = [];
foreach ($journals as $j) {
    $byCategory[$j['category']] = ($byCategory[$j['category']] ?? 0) + 1;
}
arsort($byCategory);
$topCategory = array_key_first($byCategory) ?? '—';

$categories = [
    'Uncategorized', 'Community Development', 'Communication Research',
    'Agricultural Extension', 'Food Security', 'Rural Development',
    'Climate Change', 'ICT in Agriculture', 'Nutrition', 'Education',
];
?>

<!-- ═══ Journals Section ═══ -->
<div id="section-upload-journal" class="admin-section" style="display:none;">

    <!-- Tab switcher -->
    <div style="display:flex; gap:0; margin-bottom:28px; background:#fff; border-radius:12px; padding:6px; box-shadow:var(--shadow); width:fit-content;">
        <button id="jTabPublish" onclick="switchJournalTab('publish')"
                style="padding:10px 26px; border-radius:9px; border:none; font-weight:700; font-size:13px; cursor:pointer; background:var(--primary-color); color:#fff; font-family:inherit; transition:all 0.2s;">
            <i class="fas fa-upload" style="margin-right:7px;"></i>Publish Journal
        </button>
        <button id="jTabList" onclick="switchJournalTab('list')"
                style="padding:10px 26px; border-radius:9px; border:none; font-weight:700; font-size:13px; cursor:pointer; background:transparent; color:#64748b; font-family:inherit; transition:all 0.2s;">
            <i class="fas fa-books" style="margin-right:7px;"></i>All Journals
            <span style="background:#f1f5f9; border-radius:50px; padding:2px 9px; font-size:11px; margin-left:4px;"><?= count($journals) ?></span>
        </button>
    </div>


    <!-- ═══════════ PUBLISH PANEL ═══════════ -->
    <div id="jPanelPublish">

        <!-- Stat strip -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
            <div style="background:#fff; border-radius:12px; padding:18px 22px; box-shadow:var(--shadow); display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; border-radius:12px; background:rgba(122,208,58,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-book-open" style="color:var(--primary-color); font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:26px; font-weight:800; color:var(--heading-color); line-height:1;"><?= count($journals) ?></div>
                    <div style="font-size:12px; color:#94a3b8; font-weight:600;">Total Journals</div>
                </div>
            </div>
            <div style="background:#fff; border-radius:12px; padding:18px 22px; box-shadow:var(--shadow); display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; border-radius:12px; background:rgba(59,130,246,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-tags" style="color:#3b82f6; font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:26px; font-weight:800; color:var(--heading-color); line-height:1;"><?= count($byCategory) ?></div>
                    <div style="font-size:12px; color:#94a3b8; font-weight:600;">Categories Used</div>
                </div>
            </div>
            <div style="background:#fff; border-radius:12px; padding:18px 22px; box-shadow:var(--shadow); display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; border-radius:12px; background:rgba(139,92,246,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-star" style="color:#8b5cf6; font-size:18px;"></i>
                </div>
                <div style="min-width:0;">
                    <div style="font-size:13px; font-weight:800; color:var(--heading-color); line-height:1.3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($topCategory) ?></div>
                    <div style="font-size:12px; color:#94a3b8; font-weight:600;">Top Category</div>
                </div>
            </div>
        </div>

        <!-- Publish form — two-column layout -->
        <div style="display:grid; grid-template-columns:1fr 320px; gap:22px; align-items:start;">

            <!-- Main form -->
            <div class="section-card" style="padding:28px;">
                <div class="section-header" style="margin-bottom:24px;">
                    <h3><i class="fas fa-file-upload" style="color:var(--primary-color); margin-right:8px;"></i>Journal Details</h3>
                </div>

                <div id="journal-upload-alert" style="display:none; padding:14px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; font-size:14px;"></div>

                <form id="journalUploadForm" onsubmit="handleJournalUpload(event)" enctype="multipart/form-data">

                    <div class="form-group">
                        <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">
                            Journal Title <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" class="form-control" name="title" id="journal_title"
                               placeholder="e.g. Community-Led Development: A Case Study in SE Nigeria…"
                               style="font-size:15px; font-weight:600; padding:13px 16px;" required>
                    </div>

                    <div class="form-group">
                        <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">
                            Authors <span style="color:#94a3b8; font-weight:400;">(comma-separated)</span>
                        </label>
                        <input type="text" class="form-control" name="authors" id="journal_authors"
                               placeholder="e.g. John A. Smith, Jane B. Doe">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                        <div class="form-group">
                            <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">Volume</label>
                            <input type="text" class="form-control" name="volume" placeholder="e.g. 5">
                        </div>
                        <div class="form-group">
                            <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">Issue</label>
                            <input type="text" class="form-control" name="issue" placeholder="e.g. 1">
                        </div>
                        <div class="form-group">
                            <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">Year</label>
                            <input type="text" class="form-control" name="year" placeholder="e.g. 2024">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">DOI</label>
                        <input type="text" class="form-control" name="doi" placeholder="e.g. 10.1234/jccr.2024.001">
                    </div>

                    <div class="form-group">
                        <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">
                            Publication Date <span style="color:#94a3b8; font-weight:400;">(the actual date published)</span>
                        </label>
                        <input type="date" class="form-control" name="published_date" style="font-size:13px;">
                    </div>

                    <div class="form-group">
                        <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">
                            Abstract / Short Description
                        </label>
                        <textarea class="form-control" name="abstract" id="journal_abstract" rows="5"
                                  placeholder="Paste or type the journal abstract here…"
                                  style="resize:vertical;"></textarea>
                        <div id="abstractCharCount" style="font-size:11px; color:#94a3b8; text-align:right; margin-top:4px;">0 characters</div>
                    </div>

                    <div class="form-group">
                        <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">
                            Keywords <span style="color:#94a3b8; font-weight:400;">(comma-separated)</span>
                        </label>
                        <input type="text" class="form-control" name="keywords"
                               placeholder="e.g. agroforestry, cocoa, rural development">
                    </div>

                    <button type="submit" id="btnJournalSubmit" class="btn-upload" style="width:100%; justify-content:center; padding:14px;">
                        <i class="fas fa-paper-plane" style="margin-right:8px;"></i>Publish Journal
                    </button>
                </form>
            </div>

            <!-- Sidebar: File + Category -->
            <div style="display:flex; flex-direction:column; gap:18px;">

                <!-- PDF Drag-Drop -->
                <div class="section-card" style="padding:22px;">
                    <div style="font-size:13px; font-weight:700; color:var(--heading-color); margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-file-pdf" style="color:#ef4444;"></i> Journal File
                        <span style="color:#ef4444; font-size:12px;">*</span>
                    </div>

                    <div id="pdfDropZone"
                         onclick="document.getElementById('journal_file').click()"
                         ondragover="pdfDragOver(event)" ondragleave="pdfDragLeave(event)" ondrop="pdfDrop(event)"
                         style="border:2px dashed var(--border-color); border-radius:10px; padding:28px 16px; text-align:center; cursor:pointer; background:#f8fafc; transition:all 0.2s; position:relative; overflow:hidden;">
                        <div id="pdfPlaceholder">
                            <i class="fas fa-file-pdf" style="font-size:38px; color:#ef4444; opacity:0.4; display:block; margin-bottom:10px;"></i>
                            <span style="font-size:13px; color:#64748b; font-weight:600;">Click or drag PDF / DOCX here</span><br>
                            <span style="font-size:11px; color:#b0bec5; margin-top:4px; display:block;">Max 20MB</span>
                        </div>
                        <div id="pdfSelected" style="display:none;">
                            <i class="fas fa-check-circle" style="font-size:30px; color:var(--primary-color); display:block; margin-bottom:8px;"></i>
                            <div id="pdfFileName" style="font-size:13px; font-weight:700; color:var(--heading-color); word-break:break-all;"></div>
                            <div id="pdfFileSize" style="font-size:11px; color:#94a3b8; margin-top:4px;"></div>
                        </div>
                    </div>
                    <input type="file" name="journal_file" id="journal_file"
                           accept=".pdf,.docx" style="display:none;" onchange="pdfSelected(this)" required>
                    <button type="button" id="pdfRemoveBtn" onclick="removePdf()"
                            style="display:none; width:100%; margin-top:10px; padding:8px; border:1px solid #fee2e2; border-radius:8px; background:#fff; color:#ef4444; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;">
                        <i class="fas fa-times" style="margin-right:5px;"></i>Remove File
                    </button>
                </div>

                <!-- Cover Image -->
                <div class="section-card" style="padding:22px;">
                    <div style="font-size:13px; font-weight:700; color:var(--heading-color); margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-image" style="color:#f59e0b;"></i> Cover Image
                        <span style="color:#94a3b8; font-size:11px; font-weight:normal;">(Optional)</span>
                    </div>

                    <div id="imgDropZone"
                         onclick="document.getElementById('cover_image').click()"
                         ondragover="imgDragOver(event)" ondragleave="imgDragLeave(event)" ondrop="imgDrop(event)"
                         style="border:2px dashed var(--border-color); border-radius:10px; padding:28px 16px; text-align:center; cursor:pointer; background:#f8fafc; transition:all 0.2s; position:relative; overflow:hidden;">
                        <div id="imgPlaceholder">
                            <i class="fas fa-image" style="font-size:38px; color:#f59e0b; opacity:0.4; display:block; margin-bottom:10px;"></i>
                            <span style="font-size:13px; color:#64748b; font-weight:600;">Click or drag Cover Image</span><br>
                            <span style="font-size:11px; color:#b0bec5; margin-top:4px; display:block;">JPG / PNG / WEBP (Max 5MB)</span>
                        </div>
                        <div id="imgSelected" style="display:none;">
                            <img id="imgPreview" src="" style="max-height:80px; max-width:100%; border-radius:6px; margin-bottom:8px; display:block; margin-left:auto; margin-right:auto; object-fit:contain;">
                            <div id="imgFileName" style="font-size:12px; font-weight:700; color:var(--heading-color); word-break:break-all;"></div>
                        </div>
                    </div>
                    <input type="file" name="cover_image" id="cover_image" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="imgSelected(this)">
                    <button type="button" id="imgRemoveBtn" onclick="removeImg()"
                            style="display:none; width:100%; margin-top:10px; padding:8px; border:1px solid #fee2e2; border-radius:8px; background:#fff; color:#ef4444; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;">
                        <i class="fas fa-times" style="margin-right:5px;"></i>Remove Image
                    </button>
                </div>

                <!-- Category -->
                <div class="section-card" style="padding:22px;">
                    <div style="font-size:13px; font-weight:700; color:var(--heading-color); margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-tag" style="color:var(--primary-color);"></i> Category
                    </div>
                    <select name="category" form="journalUploadForm" class="form-control" style="font-size:13px;">
                        <?php foreach($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <?php if(!empty($byCategory)): ?>
                    <div style="margin-top:16px; padding-top:14px; border-top:1px solid var(--border-color);">
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8; margin-bottom:10px;">Published by Category</div>
                        <?php foreach($byCategory as $cat => $cnt): ?>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                            <span style="font-size:12px; color:var(--heading-color); font-weight:500;"><?= htmlspecialchars($cat) ?></span>
                            <span style="background:rgba(122,208,58,0.1); color:var(--primary-color); font-size:11px; font-weight:700; padding:2px 9px; border-radius:50px;"><?= $cnt ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div><!-- /#jPanelPublish -->


    <!-- ═══════════ ALL JOURNALS PANEL ═══════════ -->
    <div id="jPanelList" style="display:none;">

        <?php if(empty($journals)): ?>
        <div style="background:#fff; border-radius:12px; padding:70px; text-align:center; color:#94a3b8; box-shadow:var(--shadow);">
            <i class="fas fa-book-open" style="font-size:52px; display:block; margin-bottom:18px; opacity:0.25;"></i>
            <h4 style="color:#cbd5e1; font-weight:700; font-size:20px; margin-bottom:8px;">No journals published yet</h4>
            <p style="font-size:14px;">Use the <strong>Publish Journal</strong> tab to add the first one.</p>
        </div>
        <?php else: ?>

        <!-- Toolbar -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:12px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="font-size:22px; font-weight:800; color:var(--heading-color);">
                    <?= count($journals) ?> <span style="font-size:14px; font-weight:500; color:#94a3b8;">journal<?= count($journals) !== 1 ? 's' : '' ?></span>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <input type="text" id="journalSearch" placeholder="Search journals…" oninput="filterJournals(this.value)"
                       style="border:1px solid var(--border-color); border-radius:8px; padding:9px 14px; font-size:13px; outline:none; font-family:inherit; width:220px;">
                <select id="journalCatFilter" onchange="filterJournals(document.getElementById('journalSearch').value)"
                        style="border:1px solid var(--border-color); border-radius:8px; padding:9px 14px; font-size:13px; outline:none; font-family:inherit; cursor:pointer;">
                    <option value="">All Categories</option>
                    <?php foreach(array_keys($byCategory) as $cat): ?>
                    <option value="<?= htmlspecialchars(strtolower($cat)) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Cards grid -->
        <div id="journalCardsGrid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px;">
            <?php foreach($journals as $j): ?>
            <div class="journal-admin-card"
                 data-title="<?= htmlspecialchars(strtolower($j['title'])) ?>"
                 data-category="<?= htmlspecialchars(strtolower($j['category'])) ?>"
                 style="background:#fff; border-radius:14px; border:1px solid var(--border-color); padding:22px; display:flex; flex-direction:column; box-shadow:var(--shadow); transition:transform 0.2s, box-shadow 0.2s;"
                 onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 30px rgba(0,0,0,0.1)'"
                 onmouseout="this.style.transform=''; this.style.boxShadow='var(--shadow)'">

                <!-- Top row: icon + category -->
                <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:14px;">
                    <?php if($j['cover_image']): ?>
                    <div style="width:48px; height:68px; background:#f8fafc; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; border:1px solid var(--border-color);">
                        <img src="<?= htmlspecialchars($j['cover_image']) ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <?php else: ?>
                    <div style="width:48px; height:48px; background:rgba(239,68,68,0.08); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-file-pdf" style="font-size:22px; color:#ef4444;"></i>
                    </div>
                    <?php endif; ?>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">
                            <span style="display:inline-block; background:rgba(122,208,58,0.1); color:var(--primary-color); font-size:10.5px; font-weight:700; padding:3px 11px; border-radius:50px; text-transform:uppercase; letter-spacing:0.5px;">
                                <?= htmlspecialchars($j['category']) ?>
                            </span>
                            <span class="status-badge" style="display:inline-block; background:<?= $j['status'] === 'published' ? '#dcfce7' : '#fef3c7' ?>; color:<?= $j['status'] === 'published' ? '#16a34a' : '#d97706' ?>; font-size:10px; font-weight:700; padding:3px 10px; border-radius:50px; text-transform:uppercase; letter-spacing:0.5px;">
                                <?= $j['status'] === 'published' ? 'Published' : 'Draft' ?>
                            </span>
                        </div>
                        <h4 style="font-size:14.5px; font-weight:700; color:var(--heading-color); line-height:1.45; margin:0;">
                            <?= htmlspecialchars(strlen($j['title']) > 80 ? substr($j['title'],0,80).'…' : $j['title']) ?>
                        </h4>
                    </div>
                </div>

                <!-- Abstract preview -->
                <?php if($j['abstract']): ?>
                <p style="font-size:12.5px; color:#64748b; line-height:1.65; margin-bottom:14px; flex:1;">
                    <?= htmlspecialchars(substr($j['abstract'],0,130)) ?>…
                </p>
                <?php else: ?>
                <p style="font-size:12px; color:#b0bec5; font-style:italic; margin-bottom:14px; flex:1;">No abstract provided.</p>
                <?php endif; ?>

                <!-- Footer -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding-top:12px; border-top:1px solid var(--border-color); margin-top:auto;">
                    <span style="font-size:11.5px; color:#94a3b8;">
                        <i class="fas fa-calendar-alt" style="margin-right:4px;"></i>
                        <?= date('d M Y', strtotime($j['created_at'])) ?>
                    </span>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <button onclick="toggleJournalStatus(<?= $j['id'] ?>, this)"
                                data-status="<?= $j['status'] ?>"
                                style="width:32px; height:32px; border:1px solid <?= $j['status'] === 'published' ? '#bbf7d0' : '#fee2e2' ?>; border-radius:8px; background:#fff; display:flex; align-items:center; justify-content:center; color:<?= $j['status'] === 'published' ? '#16a34a' : '#ef4444' ?>; cursor:pointer; transition:all 0.2s;"
                                title="<?= $j['status'] === 'published' ? 'Unpublish (hide from website)' : 'Publish (show on website)' ?>"
                                onmouseover="this.style.background='<?= $j['status'] === 'published' ? '#f0fdf4' : '#fef2f2' ?>'"
                                onmouseout="this.style.background='#fff'">
                            <i class="fas <?= $j['status'] === 'published' ? 'fa-toggle-on' : 'fa-toggle-off' ?>" style="font-size:14px;"></i>
                        </button>
                        <button onclick="openPreviewJournal(<?= htmlspecialchars(json_encode([
                            'title'       => $j['title'],
                            'authors'     => $j['authors'] ?? '',
                            'category'    => $j['category'],
                            'abstract'    => $j['abstract'] ?? '',
                            'keywords'    => $j['keywords'] ?? '',
                            'doi'         => $j['doi'] ?? '',
                            'volume'      => $j['volume'] ?? '',
                            'issue'       => $j['issue'] ?? '',
                            'year'        => $j['year'] ?? '',
                            'file_path'   => $j['file_path'],
                            'cover_image' => $j['cover_image'] ?? '',
                            'created_at'  => $j['created_at'],
                        ])) ?>)"
                                style="width:32px; height:32px; border:1px solid var(--border-color); border-radius:8px; background:#fff; display:flex; align-items:center; justify-content:center; color:#64748b; cursor:pointer; transition:all 0.2s;"
                                title="Preview journal"
                                onmouseover="this.style.borderColor='var(--primary-color)'; this.style.color='var(--primary-color)'"
                                onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='#64748b'">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </button>
                        <button onclick="openEditJournal(<?= $j['id'] ?>, <?= htmlspecialchars(json_encode($j['title'])) ?>, <?= htmlspecialchars(json_encode($j['authors'] ?? '')) ?>, <?= htmlspecialchars(json_encode($j['category'])) ?>, <?= htmlspecialchars(json_encode($j['abstract'] ?? '')) ?>, <?= htmlspecialchars(json_encode($j['keywords'] ?? '')) ?>, <?= htmlspecialchars(json_encode($j['doi'] ?? '')) ?>, <?= htmlspecialchars(json_encode($j['volume'] ?? '')) ?>, <?= htmlspecialchars(json_encode($j['issue'] ?? '')) ?>, <?= htmlspecialchars(json_encode($j['year'] ?? '')) ?>, <?= htmlspecialchars(json_encode($j['published_date'] ?? '')) ?>, <?= htmlspecialchars(json_encode($j['file_path'])) ?>, <?= htmlspecialchars(json_encode($j['cover_image'] ?? '')) ?>)"
                                style="width:32px; height:32px; border:1px solid #dbeafe; border-radius:8px; background:#fff; display:flex; align-items:center; justify-content:center; color:#3b82f6; cursor:pointer; transition:all 0.2s;"
                                title="Edit journal"
                                onmouseover="this.style.background='#eff6ff'"
                                onmouseout="this.style.background='#fff'">
                            <i class="fas fa-pencil-alt" style="font-size:12px;"></i>
                        </button>
                        <button onclick="deleteJournal(<?= $j['id'] ?>, this)"
                                style="width:32px; height:32px; border:1px solid #fee2e2; border-radius:8px; background:#fff; display:flex; align-items:center; justify-content:center; color:#ef4444; cursor:pointer; transition:all 0.2s;"
                                title="Delete journal"
                                onmouseover="this.style.background='#fef2f2'"
                                onmouseout="this.style.background='#fff'">
                            <i class="fas fa-trash-alt" style="font-size:12px;"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- No-results message (hidden by default) -->
        <div id="journalNoResults" style="display:none; text-align:center; padding:50px; color:#94a3b8;">
            <i class="fas fa-search" style="font-size:36px; display:block; margin-bottom:12px; opacity:0.3;"></i>
            <p style="font-size:14px; font-weight:600;">No journals match your search.</p>
        </div>

        <?php endif; ?>
    </div><!-- /#jPanelList -->

</div><!-- /#section-upload-journal -->

<!-- ═══ Preview Journal Modal ═══ -->
<div id="previewJournalModal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:680px; max-height:90vh; overflow-y:auto; box-shadow:0 24px 60px rgba(0,0,0,0.2);">

        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:20px 28px; border-bottom:1px solid var(--border-color);">
            <h3 style="margin:0; font-size:16px; font-weight:800; color:var(--heading-color);">
                <i class="fas fa-eye" style="color:var(--primary-color); margin-right:9px;"></i>Journal Preview
            </h3>
            <button onclick="closePreviewJournal()"
                    style="background:none; border:none; font-size:22px; cursor:pointer; color:#94a3b8; line-height:1;">&times;</button>
        </div>

        <!-- Body -->
        <div style="padding:28px;">

            <!-- Cover + meta row -->
            <div style="display:flex; gap:20px; align-items:flex-start; margin-bottom:24px;">
                <div id="pvCoverWrap" style="flex-shrink:0;">
                    <img id="pvCoverImg" src="" alt="Cover"
                         style="width:90px; height:120px; object-fit:cover; border-radius:8px; border:1px solid var(--border-color); display:none;">
                    <div id="pvCoverIcon" style="width:60px; height:60px; background:rgba(239,68,68,0.08); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-file-pdf" style="font-size:26px; color:#ef4444;"></i>
                    </div>
                </div>
                <div style="flex:1; min-width:0;">
                    <div id="pvBadges" style="display:flex; flex-wrap:wrap; gap:7px; margin-bottom:10px;"></div>
                    <h2 id="pvTitle" style="font-size:17px; font-weight:800; color:var(--heading-color); line-height:1.4; margin:0 0 8px;"></h2>
                    <p id="pvAuthors" style="font-size:13px; color:#64748b; margin:0;"></p>
                </div>
            </div>

            <!-- Info table -->
            <table id="pvInfoTable" style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:20px;">
                <tbody id="pvInfoBody"></tbody>
            </table>

            <!-- Abstract -->
            <div id="pvAbstractWrap" style="display:none; margin-bottom:20px;">
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8; margin-bottom:8px;">Abstract</div>
                <p id="pvAbstract" style="font-size:13.5px; line-height:1.8; color:#475569; background:#f8fafc; padding:16px; border-radius:10px; margin:0;"></p>
            </div>

            <!-- Keywords -->
            <div id="pvKeywordsWrap" style="display:none; margin-bottom:24px;">
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8; margin-bottom:8px;">Keywords</div>
                <div id="pvKeywords" style="display:flex; flex-wrap:wrap; gap:6px;"></div>
            </div>

            <!-- Download button -->
            <a id="pvDownload" href="#" target="_blank"
               style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; background:#ef4444; color:#fff; border-radius:9px; font-weight:700; font-size:13px; text-decoration:none;">
                <i class="fas fa-file-download"></i> Download Document
            </a>
        </div>
    </div>
</div>

<!-- ═══ Edit Journal Modal ═══ -->
<div id="editJournalModal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 24px 60px rgba(0,0,0,0.2);">

        <!-- Modal header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:22px 28px; border-bottom:1px solid var(--border-color);">
            <h3 style="margin:0; font-size:17px; font-weight:800; color:var(--heading-color);">
                <i class="fas fa-pencil-alt" style="color:#3b82f6; margin-right:9px;"></i>Edit Journal
            </h3>
            <button onclick="closeEditJournal()"
                    style="background:none; border:none; font-size:20px; cursor:pointer; color:#94a3b8; line-height:1;">&times;</button>
        </div>

        <!-- Modal body -->
        <div style="padding:28px;">
            <div id="edit-journal-alert" style="display:none; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-weight:500; font-size:13px;"></div>

            <form id="editJournalForm" onsubmit="handleJournalEdit(event)" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editJournalId">

                <!-- Title -->
                <div class="form-group" style="margin-bottom:18px;">
                    <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">
                        Journal Title <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" name="title" id="editJournalTitle" class="form-control"
                           style="font-size:14px; padding:12px 14px;" required>
                </div>

                <!-- Authors -->
                <div class="form-group" style="margin-bottom:18px;">
                    <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">Authors <span style="color:#94a3b8; font-weight:400;">(comma-separated)</span></label>
                    <input type="text" name="authors" id="editJournalAuthors" class="form-control" style="font-size:13px;" placeholder="e.g. John A. Smith, Jane B. Doe">
                </div>

                <!-- Volume / Issue / Year -->
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:18px;">
                    <div>
                        <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">Volume</label>
                        <input type="text" name="volume" id="editJournalVolume" class="form-control" style="font-size:13px;" placeholder="e.g. 5">
                    </div>
                    <div>
                        <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">Issue</label>
                        <input type="text" name="issue" id="editJournalIssue" class="form-control" style="font-size:13px;" placeholder="e.g. 1">
                    </div>
                    <div>
                        <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">Year</label>
                        <input type="text" name="year" id="editJournalYear" class="form-control" style="font-size:13px;" placeholder="e.g. 2024">
                    </div>
                </div>

                <!-- DOI -->
                <div class="form-group" style="margin-bottom:18px;">
                    <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">DOI</label>
                    <input type="text" name="doi" id="editJournalDoi" class="form-control" style="font-size:13px;" placeholder="e.g. 10.1234/jccr.2024.001">
                </div>

                <!-- Publication Date -->
                <div class="form-group" style="margin-bottom:18px;">
                    <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">Publication Date</label>
                    <input type="date" name="published_date" id="editJournalPublishedDate" class="form-control" style="font-size:13px;">
                </div>

                <!-- Category -->
                <div class="form-group" style="margin-bottom:18px;">
                    <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">Category</label>
                    <select name="category" id="editJournalCategory" class="form-control" style="font-size:13px;">
                        <?php foreach($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Abstract -->
                <div class="form-group" style="margin-bottom:18px;">
                    <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">Abstract</label>
                    <textarea name="abstract" id="editJournalAbstract" class="form-control" rows="5"
                              style="resize:vertical; font-size:13px;"></textarea>
                </div>

                <!-- Keywords -->
                <div class="form-group" style="margin-bottom:18px;">
                    <label style="font-weight:700; font-size:13px; color:var(--heading-color); display:block; margin-bottom:7px;">Keywords <span style="color:#94a3b8; font-weight:400;">(comma-separated)</span></label>
                    <input type="text" name="keywords" id="editJournalKeywords" class="form-control" style="font-size:13px;" placeholder="e.g. agroforestry, cocoa, rural development">
                </div>

                <!-- Replace PDF -->
                <div style="margin-bottom:18px; padding:16px; background:#f8fafc; border-radius:10px; border:1px solid var(--border-color);">
                    <div style="font-weight:700; font-size:13px; color:var(--heading-color); margin-bottom:6px;">
                        <i class="fas fa-file-pdf" style="color:#ef4444; margin-right:6px;"></i>Replace Journal File
                        <span style="font-weight:400; color:#94a3b8; font-size:11px;">(leave empty to keep current)</span>
                    </div>
                    <div style="font-size:12px; color:#64748b; margin-bottom:10px;">
                        Current: <strong id="editJournalCurrentFile"></strong>
                    </div>
                    <input type="file" name="journal_file" accept=".pdf,.docx"
                           style="font-size:13px; color:#475569;">
                </div>

                <!-- Replace Cover Image -->
                <div style="margin-bottom:24px; padding:16px; background:#f8fafc; border-radius:10px; border:1px solid var(--border-color);">
                    <div style="font-weight:700; font-size:13px; color:var(--heading-color); margin-bottom:8px;">
                        <i class="fas fa-image" style="color:#f59e0b; margin-right:6px;"></i>Replace Cover Image
                        <span style="font-weight:400; color:#94a3b8; font-size:11px;">(leave empty to keep current)</span>
                    </div>
                    <img id="editCoverPreview" src="" style="display:none; max-height:80px; border-radius:6px; margin-bottom:10px; object-fit:contain;">
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                           style="font-size:13px; color:#475569;">
                </div>

                <button type="submit" id="btnEditJournalSubmit"
                        style="width:100%; padding:13px; background:var(--primary-color); color:#fff; border:none; border-radius:10px; font-weight:700; font-size:14px; cursor:pointer; font-family:inherit; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-save" style="margin-right:8px;"></i>Save Changes
                </button>
            </form>
        </div>
    </div>
</div>


<script>
// ── Tab switcher ──────────────────────────────────────────────────────────────
function switchJournalTab(tab) {
    const isPublish = tab === 'publish';
    document.getElementById('jPanelPublish').style.display = isPublish ? 'block' : 'none';
    document.getElementById('jPanelList').style.display    = isPublish ? 'none'  : 'block';

    const btnP = document.getElementById('jTabPublish');
    const btnL = document.getElementById('jTabList');
    if (isPublish) {
        btnP.style.background = 'var(--primary-color)'; btnP.style.color = '#fff';
        btnL.style.background = 'transparent';          btnL.style.color = '#64748b';
    } else {
        btnL.style.background = 'var(--primary-color)'; btnL.style.color = '#fff';
        btnP.style.background = 'transparent';          btnP.style.color = '#64748b';
    }
}

// ── Abstract char counter ─────────────────────────────────────────────────────
const abstractTA = document.getElementById('journal_abstract');
if (abstractTA) {
    abstractTA.addEventListener('input', function() {
        document.getElementById('abstractCharCount').textContent = this.value.length + ' characters';
    });
}

// ── PDF file picker helpers ───────────────────────────────────────────────────
function pdfSelected(input) {
    const file = input.files[0];
    if (!file) return;
    if (file.size > 20 * 1024 * 1024) {
        alert('File must be under 20MB.');
        input.value = '';
        return;
    }
    document.getElementById('pdfPlaceholder').style.display = 'none';
    document.getElementById('pdfSelected').style.display    = 'block';
    document.getElementById('pdfFileName').textContent = file.name;
    document.getElementById('pdfFileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    document.getElementById('pdfDropZone').style.borderColor = 'var(--primary-color)';
    document.getElementById('pdfDropZone').style.background  = 'rgba(122,208,58,0.03)';
    document.getElementById('pdfRemoveBtn').style.display    = 'block';
}

function removePdf() {
    document.getElementById('journal_file').value = '';
    document.getElementById('pdfPlaceholder').style.display = 'block';
    document.getElementById('pdfSelected').style.display    = 'none';
    document.getElementById('pdfDropZone').style.borderColor = 'var(--border-color)';
    document.getElementById('pdfDropZone').style.background  = '#f8fafc';
    document.getElementById('pdfRemoveBtn').style.display    = 'none';
}

function pdfDragOver(e) {
    e.preventDefault();
    document.getElementById('pdfDropZone').style.borderColor = 'var(--primary-color)';
    document.getElementById('pdfDropZone').style.background  = 'rgba(122,208,58,0.04)';
}

function pdfDragLeave(e) {
    document.getElementById('pdfDropZone').style.borderColor = 'var(--border-color)';
    document.getElementById('pdfDropZone').style.background  = '#f8fafc';
}

function pdfDrop(e) {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file) {
        const allowed = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!allowed.includes(file.type) && !file.name.match(/\.(pdf|docx)$/i)) {
            alert('Only PDF or DOCX files are accepted.');
            return;
        }
        const input = document.getElementById('journal_file');
        const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
        pdfSelected(input);
    }
}

// ── Cover Image file picker helpers ───────────────────────────────────────────
function imgSelected(input) {
    const file = input.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
        alert('Image must be under 5MB.');
        input.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('imgPreview').src = e.target.result;
        document.getElementById('imgPlaceholder').style.display = 'none';
        document.getElementById('imgSelected').style.display    = 'block';
        document.getElementById('imgFileName').textContent = file.name;
        document.getElementById('imgDropZone').style.borderColor = '#f59e0b';
        document.getElementById('imgDropZone').style.background  = 'rgba(245,158,11,0.03)';
        document.getElementById('imgRemoveBtn').style.display    = 'block';
    }
    reader.readAsDataURL(file);
}

function removeImg() {
    document.getElementById('cover_image').value = '';
    document.getElementById('imgPlaceholder').style.display = 'block';
    document.getElementById('imgSelected').style.display    = 'none';
    document.getElementById('imgDropZone').style.borderColor = 'var(--border-color)';
    document.getElementById('imgDropZone').style.background  = '#f8fafc';
    document.getElementById('imgRemoveBtn').style.display    = 'none';
}

function imgDragOver(e) {
    e.preventDefault();
    document.getElementById('imgDropZone').style.borderColor = '#f59e0b';
    document.getElementById('imgDropZone').style.background  = 'rgba(245,158,11,0.04)';
}

function imgDragLeave(e) {
    document.getElementById('imgDropZone').style.borderColor = 'var(--border-color)';
    document.getElementById('imgDropZone').style.background  = '#f8fafc';
}

function imgDrop(e) {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file) {
        if (!file.type.match('image.*')) {
            alert('Only Image files are accepted.');
            return;
        }
        const input = document.getElementById('cover_image');
        const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
        imgSelected(input);
    }
}

// ── Submit journal upload ─────────────────────────────────────────────────────
async function handleJournalUpload(e) {
    e.preventDefault();
    const alertBox = document.getElementById('journal-upload-alert');
    const btn      = document.getElementById('btnJournalSubmit');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i>Publishing…';
    alertBox.style.display = 'none';

    const formData = new FormData(document.getElementById('journalUploadForm'));
    // Category select lives outside the form tag — add it manually
    const catEl = document.querySelector('select[name="category"][form="journalUploadForm"]');
    if (catEl) formData.set('category', catEl.value);
    
    // File input also lives outside the form tag — add it manually
    const fileInput = document.getElementById('journal_file');
    if (fileInput && fileInput.files[0]) {
        formData.set('journal_file', fileInput.files[0]);
    }
    
    // Cover image input
    const imgInput = document.getElementById('cover_image');
    if (imgInput && imgInput.files[0]) {
        formData.set('cover_image', imgInput.files[0]);
    }

    try {
        const res  = await fetch('/actions/upload_journal.php', { method: 'POST', body: formData });
        const data = await res.json();
        alertBox.style.display    = 'block';
        alertBox.style.background = data.status === 'success' ? 'rgba(122,208,58,0.1)' : 'rgba(239,68,68,0.08)';
        alertBox.style.color      = data.status === 'success' ? '#166534' : '#b91c1c';
        alertBox.style.border     = data.status === 'success' ? '1px solid rgba(122,208,58,0.25)' : '1px solid rgba(239,68,68,0.2)';
        alertBox.innerHTML = data.message;
        if (data.status === 'success') {
            document.getElementById('journalUploadForm').reset();
            removePdf();
            document.getElementById('abstractCharCount').textContent = '0 characters';
            setTimeout(() => location.reload(), 1500);
        }
    } catch(err) {
        alertBox.style.display   = 'block';
        alertBox.style.background = 'rgba(239,68,68,0.08)';
        alertBox.style.color      = '#b91c1c';
        alertBox.style.border     = '1px solid rgba(239,68,68,0.2)';
        alertBox.innerHTML = 'Upload failed. Please try again.';
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane" style="margin-right:8px;"></i>Publish Journal';
}

// ── Toggle publish/unpublish ──────────────────────────────────────────────────
async function toggleJournalStatus(id, btn) {
    btn.disabled = true;
    const formData = new FormData();
    formData.append('id', id);
    const res  = await fetch('/actions/toggle_journal_status.php', { method: 'POST', body: formData });
    const data = await res.json();
    btn.disabled = false;
    if (data.status === 'success') {
        const isPublished = data.new_status === 'published';
        btn.dataset.status          = data.new_status;
        btn.title                   = isPublished ? 'Unpublish (hide from website)' : 'Publish (show on website)';
        btn.style.borderColor       = isPublished ? '#bbf7d0' : '#fee2e2';
        btn.style.color             = isPublished ? '#16a34a' : '#ef4444';
        btn.onmouseover             = () => { btn.style.background = isPublished ? '#f0fdf4' : '#fef2f2'; };
        btn.querySelector('i').className = 'fas ' + (isPublished ? 'fa-toggle-on' : 'fa-toggle-off');

        // Update the status badge on the card if it exists
        const badge = btn.closest('.journal-admin-card')?.querySelector('.status-badge');
        if (badge) {
            badge.textContent        = isPublished ? 'Published' : 'Draft';
            badge.style.background   = isPublished ? '#dcfce7' : '#fef3c7';
            badge.style.color        = isPublished ? '#16a34a' : '#d97706';
        }
    } else {
        alert(data.message || 'Failed to update status.');
    }
}

// ── Delete journal ────────────────────────────────────────────────────────────
async function deleteJournal(id, btn) {
    if (!confirm('Delete this journal permanently?')) return;
    const formData = new FormData();
    formData.append('id', id);
    const res  = await fetch('/actions/delete_journal.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
        const card = btn.closest('.journal-admin-card');
        card.style.opacity    = '0';
        card.style.transition = 'opacity 0.3s';
        setTimeout(() => { card.remove(); checkEmptyGrid(); }, 300);
    }
}

function checkEmptyGrid() {
    const cards = document.querySelectorAll('.journal-admin-card:not([style*="opacity: 0"])');
    if (!cards.length) location.reload();
}

// ── Preview journal modal ─────────────────────────────────────────────────────
function openPreviewJournal(j) {
    // Title & authors
    document.getElementById('pvTitle').textContent   = j.title;
    document.getElementById('pvAuthors').textContent = j.authors ? '✎ ' + j.authors : '';

    // Cover image
    const img  = document.getElementById('pvCoverImg');
    const icon = document.getElementById('pvCoverIcon');
    if (j.cover_image) {
        img.src = j.cover_image; img.style.display = 'block'; icon.style.display = 'none';
    } else {
        img.style.display = 'none'; icon.style.display = 'flex';
    }

    // Badges
    const badges = document.getElementById('pvBadges');
    badges.innerHTML = '';
    if (j.category) badges.innerHTML += `<span style="background:rgba(122,208,58,0.1);color:#2d5a3d;padding:3px 12px;border-radius:50px;font-size:11px;font-weight:700;text-transform:uppercase;">${j.category}</span>`;
    let vol = '';
    if (j.volume) vol += 'Vol. ' + j.volume;
    if (j.issue)  vol += (vol ? ' &bull; ' : '') + 'No. ' + j.issue;
    if (j.year)   vol += (vol ? ' &bull; ' : '') + j.year;
    if (vol) badges.innerHTML += `<span style="background:#f1f5f9;color:#64748b;padding:3px 12px;border-radius:50px;font-size:11px;font-weight:600;">${vol}</span>`;

    // Info table rows
    const rows = [
        ['Volume',    j.volume],
        ['Issue',     j.issue],
        ['Year',      j.year],
        ['DOI',       j.doi],
        ['Published', j.created_at ? new Date(j.created_at).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : ''],
    ];
    const tbody = document.getElementById('pvInfoBody');
    tbody.innerHTML = '';
    rows.forEach(([label, val]) => {
        if (!val) return;
        tbody.innerHTML += `<tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:9px 0;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;width:110px;">${label}</td>
            <td style="padding:9px 0;color:#334155;font-size:13px;">${val}</td>
        </tr>`;
    });

    // Abstract
    const abWrap = document.getElementById('pvAbstractWrap');
    if (j.abstract) {
        document.getElementById('pvAbstract').textContent = j.abstract;
        abWrap.style.display = 'block';
    } else { abWrap.style.display = 'none'; }

    // Keywords
    const kwWrap = document.getElementById('pvKeywordsWrap');
    const kwDiv  = document.getElementById('pvKeywords');
    if (j.keywords) {
        kwDiv.innerHTML = j.keywords.split(',').map(k =>
            `<span style="background:#f1f5f9;border:1px solid #e2e8f0;color:#475569;padding:3px 11px;border-radius:50px;font-size:12px;">${k.trim()}</span>`
        ).join('');
        kwWrap.style.display = 'block';
    } else { kwWrap.style.display = 'none'; }

    // Download link
    document.getElementById('pvDownload').href = j.file_path;

    document.getElementById('previewJournalModal').style.display = 'flex';
}

function closePreviewJournal() {
    document.getElementById('previewJournalModal').style.display = 'none';
}

// ── Edit journal modal ────────────────────────────────────────────────────────
function openEditJournal(id, title, authors, category, abstract, keywords, doi, volume, issue, year, publishedDate, filePath, coverImage) {
    document.getElementById('editJournalId').value              = id;
    document.getElementById('editJournalTitle').value           = title;
    document.getElementById('editJournalAuthors').value         = authors;
    document.getElementById('editJournalAbstract').value        = abstract;
    document.getElementById('editJournalKeywords').value        = keywords;
    document.getElementById('editJournalDoi').value             = doi;
    document.getElementById('editJournalVolume').value          = volume;
    document.getElementById('editJournalIssue').value           = issue;
    document.getElementById('editJournalYear').value            = year;
    document.getElementById('editJournalPublishedDate').value   = publishedDate;
    document.getElementById('editJournalCurrentFile').textContent = filePath ? filePath.split('/').pop() : 'None';

    const catSelect = document.getElementById('editJournalCategory');
    for (let opt of catSelect.options) {
        opt.selected = opt.value === category;
    }

    const coverPreview = document.getElementById('editCoverPreview');
    if (coverImage) {
        coverPreview.src   = coverImage;
        coverPreview.style.display = 'block';
    } else {
        coverPreview.style.display = 'none';
    }

    document.getElementById('editJournalModal').style.display = 'flex';
}

function closeEditJournal() {
    document.getElementById('editJournalModal').style.display = 'none';
    document.getElementById('editJournalForm').reset();
}

async function handleJournalEdit(e) {
    e.preventDefault();
    const alertBox = document.getElementById('edit-journal-alert');
    const btn      = document.getElementById('btnEditJournalSubmit');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i>Saving…';
    alertBox.style.display = 'none';

    const formData = new FormData(document.getElementById('editJournalForm'));

    try {
        const res  = await fetch('/actions/edit_journal.php', { method: 'POST', body: formData });
        const data = await res.json();
        alertBox.style.display    = 'block';
        alertBox.style.background = data.status === 'success' ? 'rgba(122,208,58,0.1)' : 'rgba(239,68,68,0.08)';
        alertBox.style.color      = data.status === 'success' ? '#166534' : '#b91c1c';
        alertBox.style.border     = data.status === 'success' ? '1px solid rgba(122,208,58,0.25)' : '1px solid rgba(239,68,68,0.2)';
        alertBox.innerHTML = data.message;
        if (data.status === 'success') {
            setTimeout(() => { closeEditJournal(); location.reload(); }, 1200);
        }
    } catch(err) {
        alertBox.style.display    = 'block';
        alertBox.style.background = 'rgba(239,68,68,0.08)';
        alertBox.style.color      = '#b91c1c';
        alertBox.style.border     = '1px solid rgba(239,68,68,0.2)';
        alertBox.innerHTML = 'Update failed. Please try again.';
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save" style="margin-right:8px;"></i>Save Changes';
}

// ── Search + category filter ──────────────────────────────────────────────────
function filterJournals(q) {
    q = q.toLowerCase();
    const cat = (document.getElementById('journalCatFilter')?.value || '').toLowerCase();
    let visible = 0;
    document.querySelectorAll('.journal-admin-card').forEach(card => {
        const titleMatch = (card.dataset.title || '').includes(q);
        const catMatch   = !cat || (card.dataset.category || '').includes(cat);
        const show = titleMatch && catMatch;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const noRes = document.getElementById('journalNoResults');
    if (noRes) noRes.style.display = visible === 0 ? 'block' : 'none';
}
</script>
