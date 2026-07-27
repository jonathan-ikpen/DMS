<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../services/DashboardService.php';
$user = require_auth(['student']);
$service = new DashboardService($pdo);
$announcements = $service->announcements();
$timetable = $service->upcomingTimetable((int) $user['id'], 'student');
$reqStmt = $pdo->query("SELECT COUNT(*) FROM document_requirements WHERE audience IN ('student','all') AND is_active = 1");
$totalReqs = (int)$reqStmt->fetchColumn();

$upStmt = $pdo->prepare("SELECT COUNT(DISTINCT requirement_id) FROM document_uploads WHERE user_id = ?");
$upStmt->execute([$user['id']]);
$uploadCount = (int)$upStmt->fetchColumn();

$studentStmt = $pdo->prepare('SELECT level FROM students WHERE user_id = ?');
$studentStmt->execute([$user['id']]);
$studentLevel = $studentStmt->fetchColumn();

$today = date('l');
$classesTodayStmt = $pdo->prepare('SELECT COUNT(*) FROM timetable WHERE level = ? AND day_of_week = ?');
$classesTodayStmt->execute([$studentLevel, $today]);
$classesToday = (int)$classesTodayStmt->fetchColumn();

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
            <article><span>Documents</span><strong><?= $uploadCount ?> / <?= $totalReqs ?></strong></article>
            <article><span>Classes Today</span><strong><?= $classesToday ?></strong></article>
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
