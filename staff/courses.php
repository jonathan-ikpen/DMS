<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['staff']);
$courses = $pdo->query('SELECT * FROM courses ORDER BY code')->fetchAll();
$pageTitle = 'Courses';
$items = ['Dashboard' => 'staff/dashboard.php', 'Profile' => 'staff/profile.php', 'Qualifications' => 'staff/qualifications.php', 'Courses' => 'staff/courses.php', 'Timetable' => 'staff/timetable.php', 'Announcements' => 'staff/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace"><div class="workspace-heading"><div><p class="eyebrow">Staff</p><h1>Assigned courses</h1></div></div><section class="feature-grid"><?php foreach ($courses as $course): ?><article><h3><?= e($course['code']) ?></h3><p><?= e($course['title']) ?></p><small><?= e($course['level']) ?> · <?= e($course['semester']) ?></small></article><?php endforeach; ?></section></section></div><?php include __DIR__ . '/../components/footer.php'; ?>
