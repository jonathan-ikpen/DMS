<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['student']);
$statement = $pdo->prepare('SELECT students.*, users.name, users.email FROM students INNER JOIN users ON users.id = students.user_id WHERE users.id = ?');
$statement->execute([$user['id']]);
$profile = $statement->fetch();
$pageTitle = 'Student Profile';
$items = ['Dashboard' => 'student/dashboard.php', 'Profile' => 'student/profile.php', 'Payments' => 'student/payments.php', 'Documents' => 'student/documents.php', 'Timetable' => 'student/timetable.php', 'Announcements' => 'student/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace"><div class="workspace-heading"><div><p class="eyebrow">Student</p><h1>Profile</h1></div></div><section class="panel detail-list"><p><strong>Name</strong><span><?= e($profile['name'] ?? '') ?></span></p><p><strong>Email</strong><span><?= e($profile['email'] ?? '') ?></span></p><p><strong>Matric number</strong><span><?= e($profile['matric_no'] ?? '') ?></span></p><p><strong>Level</strong><span><?= e($profile['level'] ?? '') ?></span></p><p><strong>Phone</strong><span><?= e($profile['phone'] ?? '') ?></span></p></section></section></div><?php include __DIR__ . '/../components/footer.php'; ?>
