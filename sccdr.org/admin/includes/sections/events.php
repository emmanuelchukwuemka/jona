<!-- Events Manager Section -->
<div id="section-events-manager" class="admin-section" style="display:none;">
    <div class="section-card">
        <div class="section-header">
            <h3>Homepage "Upcoming Event" Controller</h3>
            <span class="badge" style="background: rgba(122, 208, 58, 0.1); color: var(--primary-color); padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Live on Site</span>
        </div>
        <form action="#" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="event_name">Event Name (Headline)</label>
                <input type="text" class="form-control" name="event_name" id="event_name" value="Agricultural Extension for Food Security and Community Empowerment">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="event_date">Event Start Date & Time (Countdown)</label>
                    <input type="datetime-local" class="form-control" name="event_date" id="event_date" value="2025-08-12T10:00">
                </div>
                <div class="form-group">
                    <label for="event_date_text">Display Date Text</label>
                    <input type="text" class="form-control" name="event_date_text" id="event_date_text" value="12TH {opening ceremony}-14TH AUGUST, 2025| 10:00:00">
                </div>
            </div>

            <div class="form-group">
                <label for="event_desc">Event Description</label>
                <textarea class="form-control" name="event_desc" id="event_desc" rows="4">The 4th Biennial Conference will bring together experts, researchers, and practitioners, to present knowledge and insights on Agricultural Extension for Food Security and Community Empowerment. The conference aims to provide a platform for discussion, collaboration, and innovation in the field of focus.</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <div class="form-group">
                    <label>Promotional Banner (Image)</label>
                    <div style="display: flex; align-items: center; gap: 15px; background: #f8f9fa; padding: 10px; border-radius: 8px;">
                        <div style="width: 60px; height: 60px; background-image: url('../assets/img/program.jpeg'); background-size: cover; border-radius: 4px;"></div>
                        <div style="flex-grow: 1;">
                            <input type="file" class="form-control" style="padding: 5px;">
                            <small style="font-size: 11px;">Current: program.jpeg</small>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Program PDF (Download)</label>
                    <div style="display: flex; align-items: center; gap: 15px; background: #f8f9fa; padding: 10px; border-radius: 8px;">
                        <div style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #fff; border-radius: 4px; color: #dc3545; font-size: 24px;"><i class="fas fa-file-pdf"></i></div>
                        <div style="flex-grow: 1;">
                            <input type="file" class="form-control" style="padding: 5px;">
                            <small style="font-size: 11px;">Current: SCCDR-2025.pdf</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 10px;">
                <label for="event_footer">Announcing Source (Footer)</label>
                <input type="text" class="form-control" name="event_footer" id="event_footer" value="Secretary, Announcing">
            </div>

            <button type="button" class="btn-upload" style="width: 100%; margin-top: 10px;">Publish to Homepage</button>
        </form>
    </div>
</div>
