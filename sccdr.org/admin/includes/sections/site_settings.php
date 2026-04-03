<!-- Site Settings Section -->
<div id="section-manage-site" class="admin-section" style="display:none;">
    <div class="section-card">
        <div class="section-header">
            <h3>System Settings</h3>
        </div>
        <p style="font-size:13px; color:#64748b; margin-bottom:24px;">
            Site-wide configuration options. Changes take effect immediately.
        </p>
        <div id="settings-alert" style="display:none; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px; font-weight:500;"></div>
        <form id="siteSettingsForm" onsubmit="saveSiteSettings(event)">
            <div class="form-group">
                <label>Homepage Hero Title</label>
                <input type="text" name="hero_title" class="form-control" placeholder="e.g. SCCDR">
            </div>
            <div class="form-group">
                <label>Contact Email</label>
                <input type="email" name="contact_email" class="form-control" placeholder="e.g. info@sccdr.org">
            </div>
            <div class="form-group">
                <label>Maintenance Mode</label>
                <select name="maintenance_mode" class="form-control">
                    <option value="0">OFF — Site is live</option>
                    <option value="1">ON — Show maintenance page</option>
                </select>
            </div>
            <button type="submit" class="btn-upload" id="btnSaveSettings">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </form>
    </div>
</div>

<script>
async function saveSiteSettings(e) {
    e.preventDefault();
    const alertEl = document.getElementById('settings-alert');
    const btn     = document.getElementById('btnSaveSettings');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    const fd = new FormData(document.getElementById('siteSettingsForm'));
    try {
        const res  = await fetch('/actions/save_settings.php', { method:'POST', body:fd });
        const data = await res.json();
        alertEl.style.display   = 'block';
        alertEl.style.background = data.status === 'success' ? 'rgba(122,208,58,0.1)' : 'rgba(239,68,68,0.08)';
        alertEl.style.color      = data.status === 'success' ? '#166534' : '#b91c1c';
        alertEl.style.border     = data.status === 'success' ? '1px solid rgba(122,208,58,0.25)' : '1px solid rgba(239,68,68,0.2)';
        alertEl.innerHTML = data.message || (data.status === 'success' ? '✓ Settings saved.' : 'Something went wrong.');
    } catch(err) {
        alertEl.style.display = 'block';
        alertEl.innerHTML = 'Network error. Please try again.';
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save"></i> Save Settings';
}
</script>
