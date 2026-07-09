<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../services/DashboardService.php';
$user = require_auth(['admin']);
$service = new DashboardService($pdo);
$stats = $service->adminStats();
$announcements = $service->announcements();
$timetable = $service->upcomingTimetable();
$pageTitle = 'Admin Dashboard';
$items = [
    'Dashboard' => 'admin/dashboard.php',
    'Students' => 'admin/users.php?role=student',
    'Staff' => 'admin/users.php?role=staff',
    'Courses' => 'admin/courses.php',
    'Timetable' => 'admin/timetable.php',
    'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php',
    'Expenses' => 'admin/expenses.php',
    'Reports' => 'admin/reports.php',
    'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php',
    'Settings' => 'admin/settings.php',
];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    
    <section class="workspace">
        <div class="workspace-heading">
            <div><p class="eyebrow">Admin</p><h1>Department Overview</h1></div>
        </div>
        
        <div class="stats-grid">
            <article><span>Students</span><strong><?= e((string) $stats['students']) ?></strong></article>
            <article><span>Staff</span><strong><?= e((string) $stats['staff']) ?></strong></article>
            <article><span>Courses</span><strong><?= e((string) $stats['courses']) ?></strong></article>
            <article><span>Balance</span><strong><?= e(money($stats['revenue'] - $stats['expenses'])) ?></strong></article>
        </div>
        
        <div class="dashboard-grid" style="margin-top: 32px;">
            <section class="panel">
                <h2>Financial Report</h2>
                <canvas id="financeChart" data-revenue="<?= e((string) $stats['revenue']) ?>" data-expenses="<?= e((string) $stats['expenses']) ?>"></canvas>
            </section>
            
            <section class="panel">
                <h2>Upcoming Classes</h2>
                <?php foreach ($timetable as $slot): ?>
                    <div class="compact-row">
                        <span><?= e($slot['day_of_week']) ?> <?= e(substr($slot['start_time'], 0, 5)) ?></span>
                        <strong><?= e($slot['code']) ?></strong>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($timetable)): ?>
                    <p style="color: var(--muted); font-size: 14px; padding: 16px;">No upcoming classes.</p>
                <?php endif; ?>
            </section>
        </div>

        <section class="panel">
            <h2>Latest Announcements</h2>
            <?php foreach ($announcements as $announcement): ?>
                <div class="compact-row">
                    <span><?= e($announcement['title']) ?></span>
                    <time style="color: var(--muted); font-size: 14px;"><?= e(date('M d', strtotime($announcement['published_at']))) ?></time>
                </div>
            <?php endforeach; ?>
            <?php if (empty($announcements)): ?>
                <p style="color: var(--muted); font-size: 14px; padding: 16px;">No announcements.</p>
            <?php endif; ?>
        </section>
    </section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
