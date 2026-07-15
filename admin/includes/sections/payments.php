<?php
// admin/includes/sections/payments.php
require_once '../includes/config.php';

// Fetch latest transactions with user details
$stmt = $pdo->prepare("
    SELECT t.*, u.first_name, u.last_name, u.email, u.membership_category, u.profile_picture 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$totalRevenue = 0;
$successfulCount = 0;
$pendingCount = 0;
foreach($transactions as $t) {
    if ($t['status'] === 'succeeded') {
        $totalRevenue += $t['amount'];
        $successfulCount++;
    } elseif ($t['status'] === 'pending') {
        $pendingCount++;
    }
}
?>

<div id="section-payments" class="admin-section fade-in">
    <!-- Premium Portal Header -->
    <div class="premium-header mb-5">
        <div class="greeting-banner-row">
            <div class="greeting-wrapper">
                <span class="user-pill-status"></span>
                <span class="greeting-text">Welcome back, Admin <span class="greeting-time">&bull; <?= date('l, d M Y') ?></span></span>
            </div>
            <div class="system-status-chip">
                <span class="status-dot-pulse"></span>
                Platform Monitoring: Online
            </div>
        </div>

        <div class="header-main-row mt-3">
            <div class="title-stack">
                <h1 class="portal-main-title">Payments</h1>
                <div class="registry-badge-subtitle">
                    <span class="badge-emerald-dot"></span>
                    Financial & Membership Registry
                </div>
            </div>
            <div class="header-action-panel">
                <button class="btn btn-emerald-outline me-2"><i class="fas fa-file-export pe-2"></i>Export CSV</button>
                <button class="btn btn-emerald-solid"><i class="fas fa-plus pe-2"></i>Add Payment</button>
            </div>
        </div>
        <p class="premium-description mt-2">Managing SCCDR transactional intelligence and membership revenue streams with precision.</p>
    </div>

    <!-- Registry Summary Strip -->
    <div class="registry-summary-strip mb-5">
        <div class="summary-segment stat-brand-left">
            <div class="seg-icon-box">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="seg-info">
                <div class="seg-label">Total Revenue</div>
                <div class="seg-value">$<?= number_format($totalRevenue, 2) ?></div>
            </div>
            <div class="seg-extra">
                <span class="trend-indicator-green"><i class="fas fa-arrow-up pe-1"></i>12%</span>
                <div class="seg-sub">Last 30 days</div>
            </div>
        </div>
        <div class="seg-divider"></div>
        <div class="summary-segment">
            <div class="seg-icon-box icon-neutral">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="seg-info">
                <div class="seg-label">Active Subscriptions</div>
                <div class="seg-value"><?= $successfulCount ?></div>
            </div>
            <div class="seg-extra">
                <span class="text-emerald font-weight-700"><?= $pendingCount ?></span>
                <div class="seg-sub">Pending verification</div>
            </div>
        </div>
        <div class="seg-divider"></div>
        <div class="summary-segment">
            <div class="seg-icon-box icon-neutral">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="seg-info">
                <div class="seg-label">Avg. Order Value</div>
                <div class="seg-value">
                    <?php 
                        $avg = $successfulCount > 0 ? $totalRevenue / $successfulCount : 0;
                        echo '$' . number_format($avg, 2);
                    ?>
                </div>
            </div>
            <div class="seg-extra">
                <span class="text-muted">Stable</span>
                <div class="seg-sub">Across all tiers</div>
            </div>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="registry-table-container">
        <div class="registry-table-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="table-h3">Transaction Log</div>
                <div class="table-search-box">
                    <i class="fas fa-search table-search-icon"></i>
                    <input type="text" class="table-search-input" placeholder="Search by name, email, or invoice ID...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-brand-registry">
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Member Account</th>
                        <th>Membership Tier</th>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Date & Time</th>
                        <th class="text-end" style="padding-right: 24px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($transactions)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="registry-empty-state">
                                <i class="fas fa-receipt mb-3"></i>
                                <h4>No Membership Transactions Found</h4>
                                <p>All member dues and payments will be logged here for administrative review.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($transactions as $t): ?>
                        <tr class="registry-row">
                            <td style="padding-left: 24px;">
                                <div class="registry-account">
                                    <div class="account-avatar">
                                        <?php if($t['profile_picture']): ?>
                                            <img src="<?= htmlspecialchars($t['profile_picture']) ?>">
                                        <?php else: ?>
                                            <div class="avatar-ph"><?= strtoupper($t['first_name'][0] . $t['last_name'][0]) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="account-info">
                                        <div class="account-name"><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?></div>
                                        <div class="account-email"><?= htmlspecialchars($t['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-tier"><?= htmlspecialchars($t['membership_category']) ?></span>
                            </td>
                            <td>
                                <code class="invoice-id"><?= htmlspecialchars(substr($t['stripe_session_id'], 0, 14)) ?>...</code>
                            </td>
                            <td>
                                <div class="registry-amount">$<?= number_format($t['amount'], 2) ?> <span class="amount-cur">USD</span></div>
                            </td>
                            <td>
                                <div class="registry-date"><?= date('d M Y', strtotime($t['created_at'])) ?></div>
                                <div class="registry-time"><?= date('h:i A', strtotime($t['created_at'])) ?></div>
                            </td>
                            <td class="text-end" style="padding-right: 24px;">
                                <?php 
                                    $statusClass = strtolower($t['status']);
                                    $statusPill = $statusClass === 'succeeded' ? 'pill-success' : ($statusClass === 'pending' ? 'pill-warning' : 'pill-error');
                                ?>
                                <span class="status-pill-registry <?= $statusPill ?>">
                                    <span class="pill-dot"></span>
                                    <?= ucfirst($t['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="registry-table-footer">
            <div class="text-muted">Found <?= count($transactions) ?> record(s)</div>
            <div class="registry-pagination">
                <button class="btn-reg-page" disabled><i class="fas fa-chevron-left"></i></button>
                <div class="reg-page-info">1 of 1</div>
                <button class="btn-reg-page" disabled><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>

<style>
/* Professional Emerald Design System - Premium Overhaul */
:root {
    --brand-emerald: #7AD03A;
    --brand-dark: #2D3E50;
    --brand-slate: #65677E;
    --brand-soft-slate: #94A3B8;
    --brand-silver: #E6E9F0;
    --brand-bg: #F4F7FC;
    --brand-white: #FFFFFF;
}

.fade-in { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

/* Premium Header Overhaul */
.greeting-banner-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.greeting-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-pill-status {
    width: 8px;
    height: 8px;
    background: var(--brand-emerald);
    border-radius: 50%;
    box-shadow: 0 0 0 4px rgba(122, 208, 58, 0.1);
}

.greeting-text {
    font-size: 13px;
    font-weight: 600;
    color: var(--brand-soft-slate);
}

.greeting-time {
    font-weight: 400;
    color: #cbd5e1;
    margin-left: 5px;
}

.system-status-chip {
    padding: 4px 10px;
    background: rgba(122, 208, 58, 0.05);
    border: 1px solid rgba(122, 208, 58, 0.1);
    color: #5d9d2d;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-dot-pulse {
    width: 6px;
    height: 6px;
    background: var(--brand-emerald);
    border-radius: 50%;
    animation: statusPulse 1.5s infinite;
}

@keyframes statusPulse { 
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.5); opacity: 0.5; }
    100% { transform: scale(1); opacity: 1; }
}

.header-main-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.portal-main-title {
    font-size: 40px;
    font-weight: 800;
    color: var(--brand-dark);
    margin: 0;
    letter-spacing: -1.2px;
}

.registry-badge-subtitle {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: -2px;
    color: var(--brand-slate);
    font-size: 16px;
    font-weight: 500;
}

.badge-emerald-dot {
    width: 6px;
    height: 6px;
    background: var(--brand-emerald);
    border-radius: 50%;
}

.premium-description {
    font-size: 14.5px;
    color: var(--brand-soft-slate);
    max-width: 600px;
}

/* Base Buttons */
.btn-emerald-solid {
    background-color: var(--brand-emerald); color: white; border: none; border-radius: 10px;
    padding: 12px 22px; font-size: 14px; font-weight: 700; transition: all 0.2s;
}
.btn-emerald-solid:hover { background-color: #69b432; transform: translateY(-1.5px); color: white; filter: drop-shadow(0 4px 8px rgba(122,208,58,0.25)); }

.btn-emerald-outline {
    background-color: white; border: 1.5px solid var(--brand-silver); color: var(--brand-dark);
    border-radius: 10px; padding: 12px 22px; font-size: 14px; font-weight: 700; transition: all 0.2s;
}
.btn-emerald-outline:hover { border-color: var(--brand-emerald); color: var(--brand-emerald); transform: translateY(-1.5px); }

/* Registry Summary Strip */
.registry-summary-strip {
    background: white; border-radius: 14px; border: 1px solid #e2e8f0; padding: 0;
    display: flex; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}

.summary-segment { flex: 1; display: flex; align-items: center; padding: 24px 30px; gap: 20px; }

.stat-brand-left { border-left: 5px solid var(--brand-emerald); border-top-left-radius: 14px; border-bottom-left-radius: 14px; }

.seg-icon-box { width: 48px; height: 48px; background-color: rgba(122, 208, 58, 0.08); color: var(--brand-emerald); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }

.seg-icon-box.icon-neutral { background-color: #f8fafc; color: #94a3b8; border: 1px solid #f1f5f9; }

.seg-label { text-transform: uppercase; font-size: 10.5px; font-weight: 800; letter-spacing: 1px; color: var(--brand-slate); margin-bottom: 4px; }

.seg-value { font-size: 26px; font-weight: 700; color: var(--brand-dark); line-height: 1; }

.seg-extra { text-align: right; flex-shrink: 0; }

.seg-sub { font-size: 11.5px; color: #94a3b8; font-weight: 500; }

.trend-indicator-green { font-weight: 700; color: #10b981; font-size: 14px; }

.seg-divider { width: 1px; height: 50px; background: #f1f5f9; flex-shrink: 0; }

/* Registry Table */
.registry-table-container { background: white; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }

.registry-table-header { padding: 22px 24px; border-bottom: 1px solid #f1f5f9; }

.table-h3 { font-weight: 700; color: var(--brand-dark); font-size: 17px; }

.table-search-box { position: relative; width: 340px; }

.table-search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }

.table-search-input { 
    width: 100%; padding: 10px 14px 10px 42px; border: 1px solid #e2e8f0; border-radius: 10px;
    font-size: 14px; background: #f8fafc; transition: all 0.2s;
}
.table-search-input:focus { outline: none; border-color: var(--brand-emerald); background: white; box-shadow: 0 0 0 4px rgba(122, 208, 58, 0.05); }

/* Table Proper */
.table-brand-registry { width: 100%; border-collapse: collapse; }

.table-brand-registry th { padding: 14px 18px; background: #fcfdfe; color: var(--brand-slate); font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; }

.registry-row:hover td { background: #fcfdfe; }

.table-brand-registry td { padding: 16px 18px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

/* Registry Cells */
.registry-account { display: flex; align-items: center; gap: 14px; }

.account-avatar { width: 40px; height: 40px; border-radius: 10px; overflow: hidden; background: var(--brand-emerald); flex-shrink: 0; }

.account-name { font-weight: 600; color: var(--brand-dark); font-size: 13.5px; }

.account-email { font-size: 11.5px; color: var(--brand-slate); }

.badge-tier { padding: 5px 10px; background: #f1f5f9; color: var(--brand-dark); font-size: 10px; font-weight: 800; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; }

.invoice-id { font-family: inherit; font-size: 11.5px; color: var(--brand-soft-slate); background: #f8fafc; padding: 3px 8px; border-radius: 5px; }

.registry-amount { font-weight: 800; color: var(--brand-dark); font-size: 15px; }

.amount-cur { font-size: 11px; color: var(--brand-soft-slate); font-weight: 600; }

.registry-date { font-weight: 700; font-size: 13px; color: var(--brand-dark); }

.registry-time { font-size: 11.5px; color: var(--brand-soft-slate); }

/* Status Pills */
.status-pill-registry { display: inline-flex; align-items: center; gap: 8px; padding: 5px 14px; border-radius: 30px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; }

.pill-dot { width: 6px; height: 6px; border-radius: 50%; }

.pill-success { background: #ecfdf5; color: #065f46; border: 1px solid rgba(16,185,129,0.1); }
.pill-success .pill-dot { background: #10b981; }

.pill-warning { background: #fffbeb; color: #92400e; border: 1px solid rgba(245,158,11,0.1); }
.pill-warning .pill-dot { background: #f59e0b; }

.pill-error { background: #fef2f2; color: #991b1b; border: 1px solid rgba(239,68,68,0.1); }
.pill-error .pill-dot { background: #ef4444; }

/* Empty State */
.registry-empty-state { padding: 50px 0; color: #cbd5e1; }
.registry-empty-state i { font-size: 48px; }
.registry-empty-state h4 { color: var(--brand-dark); font-weight: 800; margin-top: 15px; }

/* Footer Registry */
.registry-table-footer { padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; font-size: 14px; background: #fcfdfe; border-top: 1px solid #f1f5f9; }

.registry-pagination { display: flex; align-items: center; gap: 18px; }

.btn-reg-page { width: 34px; height: 34px; border: 1px solid #e2e8f0; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 13px; cursor: pointer; transition: all 0.2s; }

.btn-reg-page:hover:not(:disabled) { border-color: var(--brand-emerald); color: var(--brand-emerald); transform: scale(1.05); }

.reg-page-info { font-weight: 700; color: var(--brand-dark); }
</style>

