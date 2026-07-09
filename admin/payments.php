<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['admin']);
$payments = $pdo->query('SELECT payments.*, users.name, users.email FROM payments INNER JOIN users ON users.id = payments.user_id ORDER BY payments.created_at DESC')->fetchAll();
$pageTitle = 'Payments';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace"><div class="workspace-heading"><div><p class="eyebrow">Admin</p><h1>Payments</h1></div></div><section class="panel table-panel"><table><thead><tr><th>Student</th><th>Reference</th><th>Gateway</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead><tbody><?php foreach ($payments as $payment): ?><tr><td><?= e($payment['name']) ?></td><td><?= e($payment['reference']) ?></td><td><?= e($payment['gateway']) ?></td><td><?= e(money($payment['amount'])) ?></td><td><span class="status"><?= e($payment['status']) ?></span></td><td><?= e(date('M d, Y', strtotime($payment['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></section></section></div><?php include __DIR__ . '/../components/footer.php'; ?>
