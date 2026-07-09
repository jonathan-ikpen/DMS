<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['student']);

// Fetch active payment items
$paymentItems = $pdo->query('SELECT * FROM payment_items WHERE is_active = 1 ORDER BY name ASC')->fetchAll();

// Fetch past transactions
$stmt = $pdo->prepare('
    SELECT p.*, pi.name as item_name 
    FROM payments p 
    LEFT JOIN payment_items pi ON p.payment_item_id = pi.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC
');
$stmt->execute([$user['id']]);
$payments = $stmt->fetchAll();

$paidItemIds = [];
$pendingItemIds = [];
foreach ($payments as $p) {
    if ($p['payment_item_id']) {
        if ($p['status'] === 'paid') {
            $paidItemIds[] = $p['payment_item_id'];
        } elseif ($p['status'] === 'pending') {
            $pendingItemIds[] = $p['payment_item_id'];
        }
    }
}

$pageTitle = 'Payments';
$items = ['Dashboard' => 'student/dashboard.php', 'Profile' => 'student/profile.php', 'Payments' => 'student/payments.php', 'Documents' => 'student/documents.php', 'Timetable' => 'student/timetable.php', 'Announcements' => 'student/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Student</p>
                <h1>Fee Payments</h1>
            </div>
        </div>

        <h3 style="margin-top: 0;">Available Fees</h3>
        <section class="feature-grid" style="margin-bottom: 32px;">
            <?php foreach ($paymentItems as $item): ?>
                <?php
                $isPaid = in_array($item['id'], $paidItemIds);
                $isPending = in_array($item['id'], $pendingItemIds);
                ?>
                <article style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="flex-grow: 1;">
                        <h3 style="margin: 0 0 8px 0; font-size: 18px;"><?= e($item['name']) ?></h3>
                        <p style="font-size: 24px; font-weight: 500; color: var(--accent); margin: 0;">₦<?= number_format($item['amount'], 2) ?></p>
                    </div>
                    
                    <?php if ($isPaid): ?>
                        <button type="button" style="width: 100%; padding: 12px; background: var(--line); color: var(--muted); border: none; border-radius: var(--pill-radius); font-weight: 500; font-size: 14px; cursor: not-allowed; font-family: inherit;" disabled>Paid</button>
                    <?php elseif ($isPending): ?>
                        <form method="get" action="<?= url('checkout.php') ?>">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="cta-button" style="width: 100%; background: #d97706;">Resume Payment</button>
                        </form>
                    <?php else: ?>
                        <form method="get" action="<?= url('checkout.php') ?>">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="cta-button" style="width: 100%;">Pay Now</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
            <?php if (empty($paymentItems)): ?>
                <p style="color: var(--muted); grid-column: 1/-1;">No fees are currently active.</p>
            <?php endif; ?>
        </section>

        <h3>Payment History</h3>
        <section class="panel table-panel">
            <?php if (empty($payments)): ?>
                <p style="padding: 24px; color: var(--muted); text-align: center;">You have no payment history.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= e($payment['item_name'] ?? 'General Payment') ?></td>
                                <td style="font-family: monospace;"><?= e($payment['reference']) ?></td>
                                <td>₦<?= number_format($payment['amount'], 2) ?></td>
                                <td>
                                    <span class="status" style="<?= $payment['status'] === 'paid' ? 'background: #dcfce7; color: #166534;' : ($payment['status'] === 'pending' ? 'background: #fef9c3; color: #854d0e;' : 'background: #fee2e2; color: #991b1b;') ?>">
                                        <?= ucfirst(e($payment['status'])) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($payment['created_at'])) ?></td>
                                <td>
                                    <?php if ($payment['status'] === 'paid'): ?>
                                        <a href="<?= url('download_receipt.php?ref=' . e($payment['reference'])) ?>" class="button button-light" style="padding: 4px 12px; font-size: 12px;" target="_blank">Download</a>
                                    <?php elseif ($payment['status'] === 'pending'): ?>
                                        <a href="<?= url('verify_payment.php?reference=' . e($payment['reference'])) ?>" class="button button-dark" style="padding: 4px 12px; font-size: 12px; border-radius: var(--pill-radius);">Verify</a>
                                    <?php else: ?>
                                        <span style="color: var(--muted); font-size: 13px;">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
