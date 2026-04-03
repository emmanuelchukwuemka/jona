<!-- Newsletter Management Section -->
<div id="section-newsletters" class="admin-section" style="display:none;">
    <div class="section-header">
        <h3>Newsletter & Subscribers</h3>
        <button class="btn-upload"><i class="fas fa-plus"></i> New Campaign</button>
    </div>

    <!-- Subscriber Stats -->
    <div class="subscriber-stats">
        <div class="stat-item">
            <h4 style="margin: 0; color: var(--primary-color);">428</h4>
            <p style="font-size: 13px; color: #64748B;">Total Subscribers</p>
        </div>
        <div class="stat-item">
            <h4 style="margin: 0; color: #3498DB;">12</h4>
            <p style="font-size: 13px; color: #64748B;">New This Week</p>
        </div>
        <div class="stat-item">
            <h4 style="margin: 0; color: #E67E22;">85%</h4>
            <p style="font-size: 13px; color: #64748B;">Open Rate</p>
        </div>
    </div>

    <!-- Subscriber Table -->
    <div class="section-card">
        <div class="section-header">
            <h4>Active Subscribers</h4>
            <div class="header-actions">
                <button class="btn-icon" style="padding: 8px 15px;"><i class="fas fa-download"></i> Export CSV</button>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Email Address</th>
                    <th>Status</th>
                    <th>Subscribed On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>amara.chukwu@gmail.com</td>
                    <td><span style="color: var(--primary-color); font-weight: 600;">Subscribed</span></td>
                    <td>14 Feb 2025</td>
                    <td><i class="fas fa-trash-alt" style="color: #EF4444; cursor: pointer;"></i></td>
                </tr>
                <tr>
                    <td>john.okoro@outlook.com</td>
                    <td><span style="color: var(--primary-color); font-weight: 600;">Subscribed</span></td>
                    <td>12 Feb 2025</td>
                    <td><i class="fas fa-trash-alt" style="color: #EF4444; cursor: pointer;"></i></td>
                </tr>
                <tr>
                    <td>jane.smith@uni.edu</td>
                    <td><span style="color: var(--primary-color); font-weight: 600;">Subscribed</span></td>
                    <td>05 Feb 2025</td>
                    <td><i class="fas fa-trash-alt" style="color: #EF4444; cursor: pointer;"></i></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Newsletter Blast Form -->
    <div class="section-card" style="margin-top: 30px;">
        <div class="section-header">
            <h4>Quick Broadcast</h4>
        </div>
        <form>
            <div class="form-group">
                <label>Campaign Title</label>
                <input type="text" class="form-control" value="SCCDR | Upcoming Biennial Conference 2025 Call for Abstracts">
            </div>
            <div class="form-group">
                <label>Recipients</label>
                <select class="form-control">
                    <option>All Subscribers (428)</option>
                    <option>Recent Subscribers (Last 30 days)</option>
                    <option>Test Group (Admin Only)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Newsletter Content (HTML/Rich-text)</label>
                <textarea class="form-control" rows="10" placeholder="Write newsletter body here..."></textarea>
            </div>
            <button type="button" class="btn-upload" style="width: 100%;"><i class="fas fa-paper-plane"></i> Send Newsletter Blast</button>
        </form>
    </div>
</div>
