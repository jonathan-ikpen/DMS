<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['student']);
$slots = $pdo->query('SELECT timetable.*, courses.code, courses.title FROM timetable INNER JOIN courses ON courses.id = timetable.course_id ORDER BY day_of_week, start_time')->fetchAll();
$pageTitle = 'Timetable';
$items = ['Dashboard' => 'student/dashboard.php', 'Profile' => 'student/profile.php', 'Payments' => 'student/payments.php', 'Documents' => 'student/documents.php', 'Timetable' => 'student/timetable.php', 'Announcements' => 'student/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace"><div class="workspace-heading"><div><p class="eyebrow">Student</p><h1>Timetable</h1></div></div><section class="panel timetable-board"><?php foreach ($slots as $slot): ?><article><strong><?= e($slot['code']) ?></strong><span><?= e($slot['title']) ?></span><small><?= e($slot['day_of_week']) ?> <?= e(substr($slot['start_time'], 0, 5)) ?> - <?= e($slot['venue']) ?></small></article><?php endforeach; ?></section></section></div><?php include __DIR__ . '/../components/footer.php'; ?>
