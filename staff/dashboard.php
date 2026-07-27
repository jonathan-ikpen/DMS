<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../services/DashboardService.php';
$user = require_auth(['staff']);

$staffStmt = $pdo->prepare('SELECT id FROM staff WHERE user_id = ?');
$staffStmt->execute([$user['id']]);
$staffRecord = $staffStmt->fetch();
$staffId = $staffRecord ? (int)$staffRecord['id'] : 0;

$today = date('l');
$classesTodayStmt = $pdo->prepare('SELECT COUNT(*) FROM timetable WHERE staff_id = ? AND day_of_week = ?');
$classesTodayStmt->execute([$staffId, $today]);
$classesToday = (int)$classesTodayStmt->fetchColumn();

$qualStmt = $pdo->prepare('SELECT COUNT(*) FROM qualifications WHERE staff_id = ?');
$qualStmt->execute([$staffId]);
$qualCount = (int)$qualStmt->fetchColumn();

$service = new DashboardService($pdo);
$timetable = $service->upcomingTimetable((int) $user['id'], 'staff');
$announcements = $service->announcements();
$pageTitle = 'Staff Dashboard';
$items = [
    'Dashboard' => 'staff/dashboard.php',
    'Profile' => 'staff/profile.php',
    'Qualifications' => 'staff/qualifications.php',
    'Courses' => 'staff/courses.php',
    'Timetable' => 'staff/timetable.php',
    'Documents' => 'staff/documents.php',
    'Announcements' => 'staff/announcements.php',
];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading"><div><p class="eyebrow">Staff</p><h1>Teaching workspace</h1></div></div>
        <div class="stats-grid">
            <article><span>Assigned classes</span><strong><?= e((string) count($timetable)) ?></strong></article>
            <article><span>Announcements</span><strong><?= e((string) count($announcements)) ?></strong></article>
            <article><span>Classes Today</span><strong><?= $classesToday ?></strong></article>
            <article><span>Qualifications</span><strong><?= $qualCount ?></strong></article>
        </div>
        <section class="panel">
            <h2>Schedule</h2>
            <?php foreach ($timetable as $slot): ?>
                <div class="compact-row"><span><?= e($slot['day_of_week']) ?> <?= e(substr($slot['start_time'], 0, 5)) ?></span><strong><?= e($slot['code']) ?> - <?= e($slot['venue']) ?></strong></div>
            <?php endforeach; ?>
        </section>
    </section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
