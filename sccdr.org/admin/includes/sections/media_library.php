<!-- Media Library Section -->
<div id="section-media-library" class="admin-section" style="display:none;">
    <div class="section-header">
        <h3>Site Media Assets</h3>
        <button class="btn-upload" onclick="document.getElementById('mediaUploadInput').click()">
            <i class="fas fa-cloud-upload-alt"></i> Upload Asset
        </button>
    </div>

    <input type="file" id="mediaUploadInput" multiple accept="image/*,application/pdf,.docx"
           style="display:none;" onchange="uploadMediaFiles(this.files)">

    <div id="mediaUploadProgress" style="display:none; margin-bottom:20px;">
        <div style="background:#f1f5f9; border-radius:8px; overflow:hidden; height:6px;">
            <div id="mediaProgressBar" style="height:100%; background:var(--primary-color); width:0%; transition:width 0.3s;"></div>
        </div>
        <p style="font-size:12px; color:#94a3b8; margin-top:6px;" id="mediaProgressText">Uploading…</p>
    </div>

    <?php
    // Scan the uploads directory for real files
    $uploadBase = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/';
    $mediaFiles = [];
    $scanDirs   = ['avatars', 'posts', 'board', 'media'];
    foreach ($scanDirs as $dir) {
        $fullPath = $uploadBase . $dir;
        if (!is_dir($fullPath)) continue;
        foreach (glob($fullPath . '/*.{jpg,jpeg,png,webp,gif,pdf}', GLOB_BRACE) as $file) {
            $mediaFiles[] = [
                'name' => basename($file),
                'url'  => '/assets/uploads/' . $dir . '/' . basename($file),
                'size' => round(filesize($file) / 1024) . ' KB',
                'type' => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp','gif']) ? 'image' : 'file',
            ];
        }
    }
    ?>

    <?php if(empty($mediaFiles)): ?>
    <div style="background:#fff; border-radius:12px; padding:60px; text-align:center; color:#94a3b8; box-shadow:var(--shadow);">
        <i class="fas fa-photo-video" style="font-size:44px; display:block; margin-bottom:14px; opacity:0.3;"></i>
        <h4 style="color:#cbd5e1; font-weight:700; margin-bottom:8px;">No media uploaded yet</h4>
        <p style="font-size:13px;">Click "Upload Asset" to add images or files. They'll appear here.</p>
    </div>
    <?php else: ?>
    <div class="media-grid" id="mediaGrid">
        <?php foreach($mediaFiles as $f): ?>
        <div class="media-item">
            <?php if($f['type'] === 'image'): ?>
            <div class="media-thumb" style="background-image:url('<?= htmlspecialchars($f['url']) ?>');"></div>
            <?php else: ?>
            <div class="media-thumb" style="display:flex; align-items:center; justify-content:center; background:#f8fafc;">
                <i class="fas fa-file-pdf" style="font-size:36px; color:#ef4444;"></i>
            </div>
            <?php endif; ?>
            <div class="media-info">
                <div class="media-name" title="<?= htmlspecialchars($f['name']) ?>"><?= htmlspecialchars($f['name']) ?></div>
                <div style="font-size:11px; color:#94a3b8; margin-top:2px;"><?= $f['size'] ?></div>
                <div class="media-actions">
                    <button class="btn-icon" onclick="copyMediaUrl('<?= htmlspecialchars($f['url']) ?>')" title="Copy URL">
                        <i class="fas fa-link"></i>
                    </button>
                    <a href="<?= htmlspecialchars($f['url']) ?>" target="_blank" class="btn-icon" title="View" style="text-decoration:none; color:inherit;">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function copyMediaUrl(url) {
    navigator.clipboard.writeText(window.location.origin + url).then(() => {
        alert('URL copied: ' + window.location.origin + url);
    });
}
async function uploadMediaFiles(files) {
    if (!files.length) return;
    const progress = document.getElementById('mediaUploadProgress');
    const bar      = document.getElementById('mediaProgressBar');
    const text     = document.getElementById('mediaProgressText');
    progress.style.display = 'block';
    bar.style.width = '0%';

    let done = 0;
    for (const file of files) {
        const fd = new FormData();
        fd.append('file', file);
        try {
            await fetch('/actions/upload_media.php', { method:'POST', body:fd });
        } catch(e) {}
        done++;
        const pct = Math.round((done / files.length) * 100);
        bar.style.width = pct + '%';
        text.textContent = `Uploaded ${done} of ${files.length}…`;
    }
    text.textContent = 'Done! Refreshing…';
    setTimeout(() => location.reload(), 800);
}
</script>
