<?php
// admin/includes/sections/payments.php
require_once '../includes/config.php';

// Fetch latest transactions with user details
$stmt = $pdo->prepare("
    SELECT t.*, u.first_name, u.last_name, u.email, u.membership_category 
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total revenue (simpler version)
$totalRevenue = 0;
foreach($transactions as $t) {
    if ($t['status'] === 'succeeded') {
        $totalRevenue += $t['amount'];
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title text-muted text-uppercase mb-2" style="font-size: 13px; font-weight: 700;">Total Revenue</h5>
                <h2 class="mb-0" style="font-weight: 800; color: #1e293b;">$<?= number_format($totalRevenue, 2) ?></h2>
                <p class="text-success small mb-0" style="font-weight: 600;"><i class="fas fa-chart-line"></i> Lifetime earnings</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title text-muted text-uppercase mb-2" style="font-size: 13px; font-weight: 700;">Successful Payments</h5>
                <h2 class="mb-0" style="font-weight: 800; color: #1e293b;"><?= count($transactions) ?></h2>
                <p class="text-muted small mb-0" style="font-weight: 600;"><i class="fas fa-receipt"></i> Completed orders</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 font-weight-bold">Payment History</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i> Export CSV</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Member</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th class="text-end px-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($transactions)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-file-invoice-dollar mb-3" style="font-size: 40px; opacity: 0.3;"></i>
                                <p>No transactions found.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($transactions as $t): ?>
                        <tr>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: 700; font-size: 12px;">
                                        <?= strtoupper(substr($t['first_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($t['membership_category']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-muted"><?= htmlspecialchars($t['email']) ?></span></td>
                            <td><strong style="color: #1e293b;">$<?= number_format($t['amount'], 2) ?></strong></td>
                            <td><?= date('d M Y, h:i A', strtotime($t['created_at'])) ?></td>
                            <td class="text-end px-4">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10" style="padding: 6px 12px; border-radius: 20px; font-weight: 700;">
                                    <?= ucfirst($t['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
