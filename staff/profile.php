<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['staff']);
$statement = $pdo->prepare('SELECT staff.*, users.name, users.email FROM staff INNER JOIN users ON users.id = staff.user_id WHERE users.id = ?');
$statement->execute([$user['id']]);
$profile = $statement->fetch();
$pageTitle = 'Staff Profile';
$items = ['Dashboard' => 'staff/dashboard.php', 'Profile' => 'staff/profile.php', 'Qualifications' => 'staff/qualifications.php', 'Courses' => 'staff/courses.php', 'Timetable' => 'staff/timetable.php', 'Announcements' => 'staff/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace"><div class="workspace-heading"><div><p class="eyebrow">Staff</p><h1>Profile</h1></div></div><section class="panel detail-list"><p><strong>Name</strong><span><?= e($profile['name'] ?? '') ?></span></p><p><strong>Email</strong><span><?= e($profile['email'] ?? '') ?></span></p><p><strong>Staff number</strong><span><?= e($profile['staff_no'] ?? '') ?></span></p><p><strong>Designation</strong><span><?= e($profile['designation'] ?? '') ?></span></p><p><strong>Office</strong><span><?= e($profile['office'] ?? '') ?></span></p></section></section></div><?php include __DIR__ . '/../components/footer.php'; ?>
