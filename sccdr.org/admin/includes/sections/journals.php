<?php
require_once __DIR__ . '/../../../includes/config.php';

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
                            Abstract / Short Description
                        </label>
                        <textarea class="form-control" name="abstract" id="journal_abstract" rows="5"
                                  placeholder="Paste or type the journal abstract here…"
                                  style="resize:vertical;"></textarea>
                        <div id="abstractCharCount" style="font-size:11px; color:#94a3b8; text-align:right; margin-top:4px;">0 characters</div>
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
                    <div style="width:48px; height:48px; background:rgba(239,68,68,0.08); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-file-pdf" style="font-size:22px; color:#ef4444;"></i>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <span style="display:inline-block; background:rgba(122,208,58,0.1); color:var(--primary-color); font-size:10.5px; font-weight:700; padding:3px 11px; border-radius:50px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">
                            <?= htmlspecialchars($j['category']) ?>
                        </span>
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
                        <a href="<?= htmlspecialchars($j['file_path']) ?>" target="_blank"
                           style="width:32px; height:32px; border:1px solid var(--border-color); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#64748b; text-decoration:none; transition:all 0.2s;"
                           title="View / Download"
                           onmouseover="this.style.borderColor='var(--primary-color)'; this.style.color='var(--primary-color)'"
                           onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='#64748b'">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
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
