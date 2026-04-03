<!-- Resource Categorization Section -->
<div id="section-resources" class="admin-section" style="display:none;">
    <div class="section-header">
        <h3>Resource Library Manager</h3>
        <button class="btn-upload"><i class="fas fa-plus"></i> New Folder</button>
    </div>

    <!-- Folder View -->
    <div class="folder-grid">
        <div class="folder-item">
            <div class="folder-icon"><i class="fas fa-folder-open"></i></div>
            <div class="folder-name">Conference Proceedings</div>
            <div class="folder-meta">12 Files • 4 Categories</div>
        </div>
        <div class="folder-item" style="border-color: var(--primary-color);">
            <div class="folder-icon" style="color: var(--primary-color);"><i class="fas fa-folder-open"></i></div>
            <div class="folder-name">Keynote Addresses</div>
            <div class="folder-meta">8 Files • 2 Categories</div>
        </div>
        <div class="folder-item">
            <div class="folder-icon"><i class="fas fa-folder"></i></div>
            <div class="folder-name">Lead Papers</div>
            <div class="folder-meta">15 Files • 3 Categories</div>
        </div>
        <div class="folder-item">
            <div class="folder-icon"><i class="fas fa-archive"></i></div>
            <div class="folder-name">General Resources</div>
            <div class="folder-meta">22 Files • 5 Categories</div>
        </div>
    </div>

    <!-- File List (Folder Content) -->
    <div class="section-card" style="margin-top: 40px;">
        <div class="section-header">
            <h4>Files in "Keynote Addresses"</h4>
            <div class="header-actions">
                <button class="btn-upload" style="padding: 8px 15px;"><i class="fas fa-upload"></i> Upload to Folder</button>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Date Added</th>
                    <th>Size</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><i class="fas fa-file-pdf" style="color: #EF4444; margin-right: 8px;"></i> Keynote-Prof-I-Ogunlade.pdf</td>
                    <td>03 Mar 2025</td>
                    <td>1.2 MB</td>
                    <td><i class="fas fa-edit" style="color: var(--primary-color); cursor: pointer;"></i></td>
                </tr>
                <tr>
                    <td><i class="fas fa-file-pdf" style="color: #EF4444; margin-right: 8px;"></i> Lead-Paper-Dr-Onunkwo.pdf</td>
                    <td>02 Mar 2025</td>
                    <td>850 KB</td>
                    <td><i class="fas fa-edit" style="color: var(--primary-color); cursor: pointer;"></i></td>
                </tr>
                <tr>
                    <td><i class="fas fa-file-pdf" style="color: #EF4444; margin-right: 8px;"></i> Presidential-Address-2019.pdf</td>
                    <td>24 Feb 2025</td>
                    <td>2.1 MB</td>
                    <td><i class="fas fa-edit" style="color: var(--primary-color); cursor: pointer;"></i></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Categorization Settings -->
    <div class="section-card" style="margin-top: 30px;">
        <div class="section-header">
            <h4>Resource Category Mapping</h4>
        </div>
        <p style="font-size: 13px; color: #64748B;">This determines where files appear on the public <a href="../resources.php" target="_blank" style="color: var(--primary-color);">Resources page</a>.</p>
        <form>
            <div class="form-group">
                <label>Category Label</label>
                <input type="text" class="form-control" value="KEYNOTE ADDRESS">
            </div>
            <div class="form-group">
                <label>Display Group (Annual/Biennial)</label>
                <select class="form-control">
                    <option>2025 - 4th Biennial Conference</option>
                    <option>2021 - 3rd Biennial Conference</option>
                    <option>2019 - National Conference</option>
                </select>
            </div>
            <button type="button" class="btn-upload">Save Mapping</button>
        </form>
    </div>
</div>

