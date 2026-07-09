<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../services/DashboardService.php';
$user = require_auth(['student']);
$service = new DashboardService($pdo);
$announcements = $service->announcements();
$timetable = $service->upcomingTimetable((int) $user['id'], 'student');
$payment = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE user_id = ? AND status = "paid"');
$payment->execute([$user['id']]);
$paid = (float) $payment->fetchColumn();
$pageTitle = 'Student Dashboard';
$items = [
    'Dashboard' => 'student/dashboard.php',
    'Profile' => 'student/profile.php',
    'Payments' => 'student/payments.php',
    'Documents' => 'student/documents.php',
    'Timetable' => 'student/timetable.php',
    'Announcements' => 'student/announcements.php',
];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading"><div><p class="eyebrow">Student</p><h1>Welcome, <?= e($user['name']) ?></h1></div></div>
        <div class="stats-grid">
            <article><span>Payment status</span><strong><?= $paid > 0 ? 'Verified' : 'Pending' ?></strong></article>
            <article><span>Total paid</span><strong><?= e(money($paid)) ?></strong></article>
            <article><span>Documents</span><strong>Upload</strong></article>
            <article><span>Notifications</span><strong><?= e((string) count($announcements)) ?></strong></article>
        </div>
        <section class="panel">
            <h2>Timetable</h2>
            <?php foreach ($timetable as $slot): ?>
                <div class="compact-row"><span><?= e($slot['day_of_week']) ?> at <?= e(substr($slot['start_time'], 0, 5)) ?></span><strong><?= e($slot['code']) ?> - <?= e($slot['title']) ?></strong></div>
            <?php endforeach; ?>
        </section>
    </section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
