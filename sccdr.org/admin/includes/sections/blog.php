<?php
require_once __DIR__ . '/../../../includes/config.php';

// Ensure posts table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS `posts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(500) NOT NULL,
    `slug` varchar(520) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'News',
    `excerpt` varchar(500) DEFAULT NULL,
    `content` longtext NOT NULL,
    `featured_image` varchar(500) DEFAULT NULL,
    `status` ENUM('published','draft') NOT NULL DEFAULT 'published',
    `author` varchar(200) NOT NULL DEFAULT 'SCCDR Admin',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$publishedCount = count(array_filter($posts, fn($p) => $p['status'] === 'published'));
$draftCount     = count(array_filter($posts, fn($p) => $p['status'] === 'draft'));
?>

<!-- ═══ Blog Posts Section ═══ -->
<div id="section-upload-blog" class="admin-section" style="display:none;">

    <!-- ── Tabs ── -->
    <div style="display:flex; gap:0; margin-bottom:28px; background:#fff; border-radius:12px; padding:6px; box-shadow:var(--shadow); width:fit-content;">
        <button id="blogTabCreate" onclick="switchBlogTab('create')"
                style="padding:10px 26px; border-radius:9px; border:none; font-weight:700; font-size:13px; cursor:pointer; background:var(--primary-color); color:#fff; font-family:inherit; transition:all 0.2s;">
            <i class="fas fa-plus-circle" style="margin-right:7px;"></i>New Post
        </button>
        <button id="blogTabList" onclick="switchBlogTab('list')"
                style="padding:10px 26px; border-radius:9px; border:none; font-weight:700; font-size:13px; cursor:pointer; background:transparent; color:#64748b; font-family:inherit; transition:all 0.2s;">
            <i class="fas fa-list" style="margin-right:7px;"></i>All Posts
            <span style="background:#f1f5f9; border-radius:50px; padding:2px 9px; font-size:11px; margin-left:6px;"><?= count($posts) ?></span>
        </button>
    </div>

    <!-- ══════════════════════════════ CREATE POST PANEL ══════════════════════════════ -->
    <div id="blogPanelCreate">

        <!-- Stats strip -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
            <div style="background:#fff; border-radius:12px; padding:18px 22px; box-shadow:var(--shadow); display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; border-radius:12px; background:rgba(122,208,58,0.12); display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-newspaper" style="color:var(--primary-color); font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:22px; font-weight:800; color:var(--heading-color); line-height:1;"><?= count($posts) ?></div>
                    <div style="font-size:12px; color:#94a3b8; font-weight:600;">Total Posts</div>
                </div>
            </div>
            <div style="background:#fff; border-radius:12px; padding:18px 22px; box-shadow:var(--shadow); display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; border-radius:12px; background:rgba(59,130,246,0.1); display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-globe" style="color:#3b82f6; font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:22px; font-weight:800; color:var(--heading-color); line-height:1;"><?= $publishedCount ?></div>
                    <div style="font-size:12px; color:#94a3b8; font-weight:600;">Published</div>
                </div>
            </div>
            <div style="background:#fff; border-radius:12px; padding:18px 22px; box-shadow:var(--shadow); display:flex; align-items:center; gap:14px;">
                <div style="width:44px; height:44px; border-radius:12px; background:rgba(245,158,11,0.1); display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-pencil-alt" style="color:#f59e0b; font-size:18px;"></i>
                </div>
                <div>
                    <div style="font-size:22px; font-weight:800; color:var(--heading-color); line-height:1;"><?= $draftCount ?></div>
                    <div style="font-size:12px; color:#94a3b8; font-weight:600;">Drafts</div>
                </div>
            </div>
        </div>

        <!-- Create Form -->
        <div style="display:grid; grid-template-columns:1fr 320px; gap:22px; align-items:start;">

            <!-- Main editor column -->
            <div>
                <div class="section-card" style="padding:28px;">
                    <div class="section-header" style="margin-bottom:24px;">
                        <h3><i class="fas fa-edit" style="color:var(--primary-color); margin-right:8px;"></i>Compose Post</h3>
                    </div>

                    <div id="blog-alert" class="alert" style="display:none; padding:14px 18px; border-radius:10px; margin-bottom:18px; font-weight:500; font-size:14px;"></div>

                    <form id="blogPostForm" onsubmit="handleBlogPost(event)" enctype="multipart/form-data">

                        <!-- Title -->
                        <div class="form-group">
                            <label class="form-group label" style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">
                                Post Title <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="title" id="blog_title" class="form-control"
                                   placeholder="Enter a compelling headline…"
                                   style="font-size:16px; font-weight:600; padding:14px 16px;"
                                   oninput="generateSlugPreview(this.value)"
                                   required>
                            <div id="slugPreview" style="font-size:11px; color:#94a3b8; margin-top:6px;">
                                <i class="fas fa-link" style="margin-right:4px;"></i><span id="slugText">—</span>
                            </div>
                        </div>

                        <!-- Excerpt -->
                        <div class="form-group">
                            <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">
                                Excerpt / Summary
                                <span style="font-weight:400; color:#94a3b8; font-size:11px;">(shown on listing pages)</span>
                            </label>
                            <textarea name="excerpt" id="blog_excerpt" class="form-control" rows="2"
                                      placeholder="Short description — 1-2 sentences…"
                                      style="resize:vertical;"></textarea>
                        </div>

                        <!-- Content -->
                        <div class="form-group">
                            <label style="font-weight:700; color:var(--heading-color); font-size:13px; display:block; margin-bottom:8px;">
                                Post Content <span style="color:#ef4444;">*</span>
                            </label>
                            <!-- Mini toolbar -->
                            <div style="display:flex; gap:6px; padding:8px 10px; background:#f8fafc; border:1px solid var(--border-color); border-bottom:none; border-radius:9px 9px 0 0;">
                                <?php
                                $tools = [
                                    ['bold','<b>B</b>'],['italic','<i>I</i>'],['underline','<u>U</u>'],
                                ];
                                foreach($tools as [$cmd,$lbl]): ?>
                                <button type="button" onclick="document.execCommand('<?= $cmd ?>')"
                                        style="width:32px; height:30px; border:1px solid var(--border-color); border-radius:6px; background:#fff; cursor:pointer; font-size:13px; font-family:inherit;">
                                    <?= $lbl ?>
                                </button>
                                <?php endforeach; ?>
                                <div style="width:1px; background:var(--border-color); margin:0 4px;"></div>
                                <button type="button" onclick="document.execCommand('insertUnorderedList')"
                                        style="width:32px; height:30px; border:1px solid var(--border-color); border-radius:6px; background:#fff; cursor:pointer; font-size:13px;">
                                    <i class="fas fa-list-ul" style="font-size:11px;"></i>
                                </button>
                                <button type="button" onclick="document.execCommand('insertOrderedList')"
                                        style="width:32px; height:30px; border:1px solid var(--border-color); border-radius:6px; background:#fff; cursor:pointer; font-size:13px;">
                                    <i class="fas fa-list-ol" style="font-size:11px;"></i>
                                </button>
                            </div>
                            <div id="blog_content_editor" contenteditable="true"
                                 style="min-height:260px; padding:16px; border:1px solid var(--border-color); border-radius:0 0 9px 9px; background:#fff; font-size:14px; line-height:1.8; color:var(--heading-color); outline:none; overflow-y:auto;"
                                 data-placeholder="Write your post content here…"></div>
                            <input type="hidden" name="content" id="blog_content_hidden">
                        </div>

                        <button type="submit" class="btn-upload" id="btnBlogSubmit" style="width:100%; justify-content:center; padding:14px;">
                            <i class="fas fa-paper-plane" style="margin-right:8px;"></i>Publish Post
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar settings column -->
            <div style="display:flex; flex-direction:column; gap:18px;">

                <!-- Featured Image -->
                <div class="section-card" style="padding:22px;">
                    <div style="font-size:13px; font-weight:700; color:var(--heading-color); margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-image" style="color:var(--primary-color);"></i> Featured Image
                    </div>
                    <div id="imgDropZone"
                         onclick="document.getElementById('blog_image').click()"
                         ondragover="blogImgDragOver(event)" ondragleave="blogImgDragLeave(event)" ondrop="blogImgDrop(event)"
                         style="border:2px dashed var(--border-color); border-radius:10px; padding:24px 16px; text-align:center; cursor:pointer; background:#f8fafc; transition:all 0.2s; position:relative; overflow:hidden;">
                        <div id="imgPlaceholder">
                            <i class="fas fa-cloud-upload-alt" style="font-size:28px; color:#cbd5e1; margin-bottom:8px; display:block;"></i>
                            <span style="font-size:12px; color:#94a3b8; font-weight:600;">Click or drag to upload</span><br>
                            <span style="font-size:11px; color:#b0bec5;">JPG, PNG, WebP — max 3MB</span>
                        </div>
                        <img id="imgPreview" src="" alt="" style="display:none; width:100%; border-radius:8px; object-fit:cover; max-height:180px;">
                    </div>
                    <input type="file" name="featured_image" id="blog_image"
                           accept="image/jpeg,image/png,image/webp"
                           style="display:none;" onchange="previewBlogImage(event)">
                    <button type="button" id="removeImgBtn" onclick="removeBlogImage()" style="display:none; width:100%; margin-top:10px; padding:8px; border:1px solid #fee2e2; border-radius:8px; background:#fff; color:#ef4444; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;">
                        <i class="fas fa-trash-alt" style="margin-right:5px;"></i>Remove Image
                    </button>
                </div>

                <!-- Post Settings -->
                <div class="section-card" style="padding:22px;">
                    <div style="font-size:13px; font-weight:700; color:var(--heading-color); margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-cog" style="color:var(--primary-color);"></i> Post Settings
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px; font-weight:700; color:#64748b; display:block; margin-bottom:7px; text-transform:uppercase; letter-spacing:0.5px;">Category</label>
                        <select name="category" class="form-control" style="font-size:13px;">
                            <option value="News">News</option>
                            <option value="Conferences">Conferences</option>
                            <option value="Research">Research</option>
                            <option value="Announcements">Announcements</option>
                            <option value="Events">Events</option>
                            <option value="Journals">Journals</option>
                            <option value="Uncategorized">Uncategorized</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px; font-weight:700; color:#64748b; display:block; margin-bottom:7px; text-transform:uppercase; letter-spacing:0.5px;">Author</label>
                        <input type="text" name="author" class="form-control" value="SCCDR Admin" style="font-size:13px;">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-size:12px; font-weight:700; color:#64748b; display:block; margin-bottom:7px; text-transform:uppercase; letter-spacing:0.5px;">Status</label>
                        <div style="display:flex; gap:8px;">
                            <label style="flex:1; display:flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid var(--border-color); border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; transition:all 0.2s;" id="statusLabelPublished">
                                <input type="radio" name="status" value="published" checked onchange="updateStatusLabel()"> Published
                            </label>
                            <label style="flex:1; display:flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid var(--border-color); border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; transition:all 0.2s;" id="statusLabelDraft">
                                <input type="radio" name="status" value="draft" onchange="updateStatusLabel()"> Draft
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ══════════════════════════════ ALL POSTS PANEL ══════════════════════════════ -->
    <div id="blogPanelList" style="display:none;">

        <?php if(empty($posts)): ?>
        <div style="background:#fff; border-radius:12px; padding:60px; text-align:center; color:#94a3b8; box-shadow:var(--shadow);">
            <i class="fas fa-newspaper" style="font-size:48px; display:block; margin-bottom:16px; opacity:0.3;"></i>
            <h4 style="color:#cbd5e1; font-weight:700; margin-bottom:8px;">No posts yet</h4>
            <p style="font-size:14px;">Create your first post using the "New Post" tab.</p>
        </div>
        <?php else: ?>

        <div class="section-card" style="padding:0; overflow:hidden;">
            <!-- Table Header -->
            <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid var(--border-color);">
                <h3 style="font-size:17px; font-weight:700; color:var(--heading-color); margin:0;">All Posts</h3>
                <input type="text" placeholder="Search posts…" oninput="filterPosts(this.value)"
                       style="border:1px solid var(--border-color); border-radius:8px; padding:8px 14px; font-size:13px; outline:none; width:200px; font-family:inherit;">
            </div>
            <div class="table-responsive">
                <table style="width:100%; border-collapse:collapse; font-size:13.5px;" id="postsTable">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:12px 20px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8; font-weight:700; border-bottom:1px solid var(--border-color);">Post</th>
                            <th style="padding:12px 20px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8; font-weight:700; border-bottom:1px solid var(--border-color);">Category</th>
                            <th style="padding:12px 20px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8; font-weight:700; border-bottom:1px solid var(--border-color);">Author</th>
                            <th style="padding:12px 20px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8; font-weight:700; border-bottom:1px solid var(--border-color);">Status</th>
                            <th style="padding:12px 20px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8; font-weight:700; border-bottom:1px solid var(--border-color);">Date</th>
                            <th style="padding:12px 20px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8; font-weight:700; border-bottom:1px solid var(--border-color);">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="postsTableBody">
                        <?php foreach($posts as $post): ?>
                        <tr style="border-bottom:1px solid var(--border-color); transition:background 0.15s;"
                            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''"
                            data-title="<?= htmlspecialchars(strtolower($post['title'])) ?>">
                            <td style="padding:14px 20px;">
                                <div style="display:flex; align-items:center; gap:14px;">
                                    <?php if($post['featured_image']): ?>
                                    <img src="<?= htmlspecialchars($post['featured_image']) ?>" alt=""
                                         style="width:52px; height:40px; border-radius:8px; object-fit:cover; flex-shrink:0; border:1px solid var(--border-color);">
                                    <?php else: ?>
                                    <div style="width:52px; height:40px; border-radius:8px; background:rgba(122,208,58,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i class="fas fa-image" style="font-size:16px; color:#cbd5e1;"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <div style="font-weight:700; color:var(--heading-color); margin-bottom:3px;">
                                            <?= htmlspecialchars(strlen($post['title']) > 55 ? substr($post['title'],0,55).'…' : $post['title']) ?>
                                        </div>
                                        <?php if($post['excerpt']): ?>
                                        <div style="font-size:11.5px; color:#94a3b8;">
                                            <?= htmlspecialchars(substr($post['excerpt'],0,65)) ?>…
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:14px 20px;">
                                <span style="background:rgba(122,208,58,0.1); color:#144525; padding:4px 12px; border-radius:50px; font-size:11.5px; font-weight:700;">
                                    <?= htmlspecialchars($post['category']) ?>
                                </span>
                            </td>
                            <td style="padding:14px 20px; color:#475569; font-size:13px;"><?= htmlspecialchars($post['author']) ?></td>
                            <td style="padding:14px 20px;">
                                <?php if($post['status'] === 'published'): ?>
                                <span style="background:rgba(59,130,246,0.1); color:#3b82f6; padding:4px 12px; border-radius:50px; font-size:11.5px; font-weight:700;">
                                    <i class="fas fa-globe" style="margin-right:4px;"></i>Published
                                </span>
                                <?php else: ?>
                                <span style="background:rgba(245,158,11,0.1); color:#f59e0b; padding:4px 12px; border-radius:50px; font-size:11.5px; font-weight:700;">
                                    <i class="fas fa-pencil-alt" style="margin-right:4px;"></i>Draft
                                </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:14px 20px; color:#64748b; font-size:12.5px;"><?= date('d M Y', strtotime($post['created_at'])) ?></td>
                            <td style="padding:14px 20px;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <a href="/post/<?= htmlspecialchars($post['category']) ?>/<?= htmlspecialchars($post['slug']) ?>"
                                       target="_blank"
                                       style="width:32px; height:32px; border:1px solid var(--border-color); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#64748b; text-decoration:none; transition:all 0.2s;"
                                       title="View post"
                                       onmouseover="this.style.borderColor='var(--primary-color)'; this.style.color='var(--primary-color)'"
                                       onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='#64748b'">
                                        <i class="fas fa-eye" style="font-size:12px;"></i>
                                    </a>
                                    <button onclick="deletePost(<?= $post['id'] ?>, this)"
                                            style="width:32px; height:32px; border:1px solid #fee2e2; border-radius:8px; background:#fff; display:flex; align-items:center; justify-content:center; color:#ef4444; cursor:pointer; transition:all 0.2s;"
                                            title="Delete post"
                                            onmouseover="this.style.background='#fef2f2'"
                                            onmouseout="this.style.background='#fff'">
                                        <i class="fas fa-trash-alt" style="font-size:12px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div><!-- /#section-upload-blog -->


<script>
// ── Tab switcher ──────────────────────────────────────────────────────────────
function switchBlogTab(tab) {
    const isCreate = tab === 'create';
    document.getElementById('blogPanelCreate').style.display = isCreate ? 'block' : 'none';
    document.getElementById('blogPanelList').style.display   = isCreate ? 'none'  : 'block';

    const btnCreate = document.getElementById('blogTabCreate');
    const btnList   = document.getElementById('blogTabList');
    if (isCreate) {
        btnCreate.style.background = 'var(--primary-color)';
        btnCreate.style.color      = '#fff';
        btnList.style.background   = 'transparent';
        btnList.style.color        = '#64748b';
    } else {
        btnList.style.background   = 'var(--primary-color)';
        btnList.style.color        = '#fff';
        btnCreate.style.background = 'transparent';
        btnCreate.style.color      = '#64748b';
    }
}

// ── Slug preview ──────────────────────────────────────────────────────────────
function generateSlugPreview(title) {
    const slug = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .substring(0, 80);
    document.getElementById('slugText').textContent = '/post/category/' + (slug || '—');
}

// ── Status radio highlight ────────────────────────────────────────────────────
function updateStatusLabel() {
    const published = document.querySelector('input[name="status"][value="published"]').checked;
    document.getElementById('statusLabelPublished').style.borderColor = published ? 'var(--primary-color)' : 'var(--border-color)';
    document.getElementById('statusLabelPublished').style.color       = published ? 'var(--primary-color)' : '';
    document.getElementById('statusLabelDraft').style.borderColor     = !published ? '#f59e0b' : 'var(--border-color)';
    document.getElementById('statusLabelDraft').style.color           = !published ? '#f59e0b' : '';
}
updateStatusLabel();

// ── Featured image picker ─────────────────────────────────────────────────────
function previewBlogImage(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 3 * 1024 * 1024) { alert('Image must be under 3MB.'); e.target.value = ''; return; }
    const reader = new FileReader();
    reader.onload = ev => {
        document.getElementById('imgPlaceholder').style.display = 'none';
        const img = document.getElementById('imgPreview');
        img.src = ev.target.result;
        img.style.display = 'block';
        document.getElementById('imgDropZone').style.borderColor = 'var(--primary-color)';
        document.getElementById('removeImgBtn').style.display = 'block';
    };
    reader.readAsDataURL(file);
}
function removeBlogImage() {
    document.getElementById('imgPreview').style.display = 'none';
    document.getElementById('imgPreview').src = '';
    document.getElementById('imgPlaceholder').style.display = 'block';
    document.getElementById('imgDropZone').style.borderColor = 'var(--border-color)';
    document.getElementById('removeImgBtn').style.display = 'none';
    document.getElementById('blog_image').value = '';
}
function blogImgDragOver(e)  { e.preventDefault(); document.getElementById('imgDropZone').style.borderColor='var(--primary-color)'; }
function blogImgDragLeave(e) { document.getElementById('imgDropZone').style.borderColor='var(--border-color)'; }
function blogImgDrop(e) {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const input = document.getElementById('blog_image');
        const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
        previewBlogImage({ target: input });
    }
}

// ── Contenteditable placeholder ───────────────────────────────────────────────
const editor = document.getElementById('blog_content_editor');
if (editor) {
    editor.addEventListener('focus', function() {
        if (this.textContent.trim() === '') this.innerHTML = '';
    });
    editor.style.cssText += ';position:relative;';
}

// ── Submit blog post ──────────────────────────────────────────────────────────
async function handleBlogPost(e) {
    e.preventDefault();
    const alertBox = document.getElementById('blog-alert');
    const btn      = document.getElementById('btnBlogSubmit');
    const form     = document.getElementById('blogPostForm');

    // Copy contenteditable HTML to hidden input
    document.getElementById('blog_content_hidden').value =
        document.getElementById('blog_content_editor').innerHTML;

    const title   = document.getElementById('blog_title').value.trim();
    const content = document.getElementById('blog_content_hidden').value.trim();
    if (!title)   { showBlogAlert('danger', 'Please enter a post title.'); return; }
    if (!content || content === '<br>') { showBlogAlert('danger', 'Please write some content.'); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i>Publishing…';

    const fd = new FormData(form);

    try {
        const res  = await fetch('/actions/create_post.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.status === 'success') {
            showBlogAlert('success', '✓ Post published successfully!');
            form.reset();
            document.getElementById('blog_content_editor').innerHTML = '';
            removeBlogImage();
            generateSlugPreview('');
            setTimeout(() => location.reload(), 1400);
        } else {
            showBlogAlert('danger', data.message || 'Something went wrong.');
        }
    } catch(err) {
        showBlogAlert('danger', 'Network error. Please try again.');
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane" style="margin-right:8px;"></i>Publish Post';
}

function showBlogAlert(type, msg) {
    const box = document.getElementById('blog-alert');
    box.style.display = 'block';
    box.style.background  = type === 'success' ? 'rgba(122,208,58,0.1)' : 'rgba(239,68,68,0.08)';
    box.style.color       = type === 'success' ? '#166534' : '#b91c1c';
    box.style.border      = type === 'success' ? '1px solid rgba(122,208,58,0.25)' : '1px solid rgba(239,68,68,0.2)';
    box.innerHTML = msg;
    box.scrollIntoView({ behavior:'smooth', block:'nearest' });
}

// ── Delete post ───────────────────────────────────────────────────────────────
async function deletePost(id, btn) {
    if (!confirm('Delete this post permanently?')) return;
    const fd = new FormData(); fd.append('id', id);
    const res  = await fetch('/actions/delete_post.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.status === 'success') {
        btn.closest('tr').style.opacity = '0';
        btn.closest('tr').style.transition = 'opacity 0.3s';
        setTimeout(() => location.reload(), 350);
    }
}

// ── Search posts ──────────────────────────────────────────────────────────────
function filterPosts(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#postsTableBody tr').forEach(row => {
        row.style.display = (row.dataset.title || '').includes(q) ? '' : 'none';
    });
}
</script>
