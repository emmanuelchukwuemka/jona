<?php
require_once '../../../../includes/config.php';

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
?>

<!-- Upload Journal Section -->
<div id="section-upload-journal" class="admin-section" style="display:none;">
    <div class="section-card">
        <div class="section-header">
            <h3>Publish New Journal Article</h3>
        </div>
        <div id="journal-upload-alert" class="alert d-none mb-3" role="alert"></div>
        <form id="journalUploadForm" onsubmit="handleJournalUpload(event)" enctype="multipart/form-data">
            <div class="form-group">
                <label for="journal_title">Journal Title</label>
                <input type="text" class="form-control" name="title" id="journal_title" placeholder="Enter journal title..." required>
            </div>
            <div class="form-group">
                <label for="journal_category">Category</label>
                <select class="form-control" name="category" id="journal_category">
                    <option value="Uncategorized">Uncategorized</option>
                    <option value="Community Development">Community Development</option>
                    <option value="Communication Research">Communication Research</option>
                    <option value="Agricultural Extension">Agricultural Extension</option>
                    <option value="Food Security">Food Security</option>
                </select>
            </div>
            <div class="form-group">
                <label for="journal_file">Upload Document (PDF/DOCX)</label>
                <input type="file" class="form-control" name="journal_file" id="journal_file" accept=".pdf,.docx" required>
            </div>
            <div class="form-group">
                <label for="journal_abstract">Short Description / Abstract</label>
                <textarea class="form-control" name="abstract" id="journal_abstract" rows="4" placeholder="Enter abstract or short description..."></textarea>
            </div>
            <button type="submit" id="btnJournalSubmit" class="btn-upload">
                <i class="fas fa-upload"></i> Publish Journal
            </button>
        </form>
    </div>

    <!-- Published Journals List -->
    <div class="section-card" style="margin-top: 30px;">
        <div class="section-header">
            <h3>Published Journals (<?php echo count($journals); ?>)</h3>
        </div>
        <?php if(empty($journals)): ?>
            <p style="color: #94a3b8; text-align:center; padding: 40px;">No journals published yet. Upload one above!</p>
        <?php else: ?>
            <div class="table-responsive">
                <table style="width:100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid var(--border-color);">
                            <th style="padding: 12px 15px; text-align:left;">#</th>
                            <th style="padding: 12px 15px; text-align:left;">Title</th>
                            <th style="padding: 12px 15px; text-align:left;">Category</th>
                            <th style="padding: 12px 15px; text-align:left;">Date</th>
                            <th style="padding: 12px 15px; text-align:left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($journals as $i => $j): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 15px; color: #94a3b8;"><?php echo $i+1; ?></td>
                            <td style="padding: 12px 15px; font-weight: 600;"><?php echo htmlspecialchars($j['title']); ?></td>
                            <td style="padding: 12px 15px;">
                                <span style="background: rgba(122,208,58,0.1); color: var(--primary-color); padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: 600;">
                                    <?php echo htmlspecialchars($j['category']); ?>
                                </span>
                            </td>
                            <td style="padding: 12px 15px; color: #64748b;"><?php echo date('d M Y', strtotime($j['created_at'])); ?></td>
                            <td style="padding: 12px 15px;">
                                <a href="<?php echo htmlspecialchars($j['file_path']); ?>" target="_blank" 
                                   style="color: var(--primary-color); font-weight: 600; margin-right: 15px;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <button onclick="deleteJournal(<?php echo $j['id']; ?>, this)" 
                                        style="background:none; border:none; color:#ef4444; font-weight:600; cursor:pointer;">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function handleJournalUpload(e) {
    e.preventDefault();
    const alertBox = document.getElementById('journal-upload-alert');
    const btn = document.getElementById('btnJournalSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing...';

    const formData = new FormData(document.getElementById('journalUploadForm'));
    try {
        const res = await fetch('/actions/upload_journal.php', { method: 'POST', body: formData });
        const data = await res.json();
        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        alertBox.classList.add(data.status === 'success' ? 'alert-success' : 'alert-danger');
        alertBox.innerHTML = data.message;
        if (data.status === 'success') {
            document.getElementById('journalUploadForm').reset();
            setTimeout(() => location.reload(), 1500);
        }
    } catch(err) {
        alertBox.classList.remove('d-none');
        alertBox.classList.add('alert-danger');
        alertBox.innerHTML = 'Upload failed. Please try again.';
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-upload"></i> Publish Journal';
}

async function deleteJournal(id, btn) {
    if(!confirm('Delete this journal permanently?')) return;
    const formData = new FormData();
    formData.append('id', id);
    const res = await fetch('/actions/delete_journal.php', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.status === 'success') location.reload();
}
</script>

