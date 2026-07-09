<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['staff']);
$statement = $pdo->prepare('SELECT qualifications.* FROM qualifications INNER JOIN staff ON staff.id = qualifications.staff_id WHERE staff.user_id = ? ORDER BY year_awarded DESC');
$statement->execute([$user['id']]);
$qualifications = $statement->fetchAll();
$pageTitle = 'Qualifications';
$items = ['Dashboard' => 'staff/dashboard.php', 'Profile' => 'staff/profile.php', 'Qualifications' => 'staff/qualifications.php', 'Courses' => 'staff/courses.php', 'Timetable' => 'staff/timetable.php', 'Announcements' => 'staff/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace"><div class="workspace-heading"><div><p class="eyebrow">Staff</p><h1>Qualifications</h1></div></div><section class="panel"><?php foreach ($qualifications as $item): ?><div class="compact-row"><span><?= e($item['institution']) ?></span><strong><?= e($item['qualification']) ?>, <?= e((string) $item['year_awarded']) ?></strong></div><?php endforeach; ?></section></section></div><?php include __DIR__ . '/../components/footer.php'; ?>
