<!-- Events Manager Section -->
<div id="section-events-manager" class="admin-section" style="display:none;">
    <div class="section-card">
        <div class="section-header">
            <h3>Homepage Event Controller</h3>
            <span style="background:rgba(122,208,58,0.1); color:var(--primary-color); padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600;">Live on Site</span>
        </div>
        <p style="font-size:13px; color:#64748b; margin-bottom:24px;">
            Update the event displayed on the homepage countdown banner. Leave fields blank to hide the event section.
        </p>
        <div id="event-alert" style="display:none; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px; font-weight:500;"></div>
        <form id="eventForm" onsubmit="saveEvent(event)" enctype="multipart/form-data">
            <div class="form-group">
                <label>Event Name (Headline) *</label>
                <input type="text" name="event_name" class="form-control" placeholder="e.g. 5th Biennial Conference of SCCDR" required>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Event Start Date &amp; Time</label>
                    <input type="datetime-local" name="event_date" class="form-control">
                </div>
                <div class="form-group">
                    <label>Display Date Text</label>
                    <input type="text" name="event_date_text" class="form-control" placeholder="e.g. 12TH–14TH AUGUST 2027 | 10:00">
                </div>
            </div>
            <div class="form-group">
                <label>Event Description</label>
                <textarea name="event_desc" class="form-control" rows="4" placeholder="Short description of the event…"></textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Promotional Banner Image</label>
                    <input type="file" name="event_banner" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>
                <div class="form-group">
                    <label>Programme PDF (Download)</label>
                    <input type="file" name="event_pdf" class="form-control" accept="application/pdf">
                </div>
            </div>
            <div class="form-group">
                <label>Announcing Source (Footer credit)</label>
                <input type="text" name="event_footer" class="form-control" placeholder="e.g. Secretary, Announcing">
            </div>
            <button type="submit" class="btn-upload" style="width:100%; margin-top:10px;" id="btnSaveEvent">
                <i class="fas fa-broadcast-tower"></i> Publish to Homepage
            </button>
        </form>
    </div>
</div>

<script>
async function saveEvent(e) {
    e.preventDefault();
    const alertEl = document.getElementById('event-alert');
    const btn     = document.getElementById('btnSaveEvent');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    const fd = new FormData(document.getElementById('eventForm'));
    try {
        const res  = await fetch('/actions/save_event.php', { method:'POST', body:fd });
        const data = await res.json();
        alertEl.style.display  = 'block';
        alertEl.style.background = data.status === 'success' ? 'rgba(122,208,58,0.1)' : 'rgba(239,68,68,0.08)';
        alertEl.style.color      = data.status === 'success' ? '#166534' : '#b91c1c';
        alertEl.style.border     = data.status === 'success' ? '1px solid rgba(122,208,58,0.25)' : '1px solid rgba(239,68,68,0.2)';
        alertEl.innerHTML = data.message || (data.status === 'success' ? '✓ Event published to homepage.' : 'Something went wrong.');
        if (data.status === 'success') document.getElementById('eventForm').reset();
    } catch(err) {
        alertEl.style.display = 'block';
        alertEl.innerHTML = 'Network error. Please try again.';
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-broadcast-tower"></i> Publish to Homepage';
}
</script>
