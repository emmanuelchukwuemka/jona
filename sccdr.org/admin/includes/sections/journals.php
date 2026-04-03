<!-- Upload Journal Section -->
<div id="section-upload-journal" class="admin-section" style="display:none;">
    <div class="section-card">
        <div class="section-header">
            <h3>Publish New Journal Article</h3>
        </div>
        <form action="#" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="journal_title">Journal Title</label>
                <input type="text" class="form-control" name="title" id="journal_title" placeholder="Enter journal title...">
            </div>
            <div class="form-group">
                <label for="journal_category">Category</label>
                <select class="form-control" name="category" id="journal_category">
                    <option value="Uncategorized">Uncategorized</option>
                    <option value="Community Development">Community Development</option>
                    <option value="Communication Research">Communication Research</option>
                </select>
            </div>
            <div class="form-group">
                <label for="journal_file">Upload Document (PDF/DOCX)</label>
                <input type="file" class="form-control" name="journal_file" id="journal_file" accept=".pdf, .docx">
            </div>
            <div class="form-group">
                <label for="journal_abstract">Short Description</label>
                <textarea class="form-control" name="abstract" id="journal_abstract" rows="4"></textarea>
            </div>
            <button type="button" class="btn-upload">Publish Article</button>
        </form>
    </div>
</div>
