<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['staff']);
$stmt = $pdo->prepare('SELECT DISTINCT courses.* FROM courses INNER JOIN timetable ON courses.id = timetable.course_id INNER JOIN staff ON staff.id = timetable.staff_id WHERE staff.user_id = ? ORDER BY courses.code');
$stmt->execute([$user['id']]);
$courses = $stmt->fetchAll();
$pageTitle = 'Courses';
$items = ['Dashboard' => 'staff/dashboard.php', 'Profile' => 'staff/profile.php', 'Qualifications' => 'staff/qualifications.php', 'Courses' => 'staff/courses.php', 'Timetable' => 'staff/timetable.php', 'Documents' => 'staff/documents.php', 'Announcements' => 'staff/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Staff</p>
                <h1>Assigned courses</h1>
            </div>
        </div>
        
        <?php if (empty($courses)): ?>
            <div class="panel" style="text-align: center; padding: 3rem 1rem;">
                <p style="color: var(--text-muted); margin: 0;">You have not been assigned to teach any courses yet.</p>
            </div>
        <?php else: ?>
            <section class="feature-grid">
                <?php foreach ($courses as $course): ?>
                    <article>
                        <h3><?= e($course['code']) ?></h3>
                        <p><?= e($course['title']) ?></p>
                        <small><?= e($course['level']) ?> &middot; <?= e($course['semester']) ?></small>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
