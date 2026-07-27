<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['staff']);
$staffStmt = $pdo->prepare('SELECT id FROM staff WHERE user_id = ?');
$staffStmt->execute([$user['id']]);
$staffId = $staffStmt->fetchColumn();

$stmt = $pdo->prepare('SELECT timetable.*, courses.code, courses.title FROM timetable INNER JOIN courses ON courses.id = timetable.course_id WHERE timetable.staff_id = ? ORDER BY day_of_week, start_time');
$stmt->execute([$staffId]);
$slots = $stmt->fetchAll();
$pageTitle = 'Timetable';
$items = ['Dashboard' => 'staff/dashboard.php', 'Profile' => 'staff/profile.php', 'Qualifications' => 'staff/qualifications.php', 'Courses' => 'staff/courses.php', 'Timetable' => 'staff/timetable.php', 'Documents' => 'staff/documents.php', 'Announcements' => 'staff/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace"><div class="workspace-heading"><div><p class="eyebrow">Staff</p><h1>Timetable</h1></div></div><section class="panel timetable-board"><?php foreach ($slots as $slot): ?><article><strong><?= e($slot['code']) ?></strong><span><?= e($slot['title']) ?></span><small><?= e($slot['day_of_week']) ?> <?= e(substr($slot['start_time'], 0, 5)) ?> - <?= e($slot['venue']) ?></small></article><?php endforeach; ?></section></section></div><?php include __DIR__ . '/../components/footer.php'; ?>
