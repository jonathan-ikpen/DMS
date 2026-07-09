<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['admin']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'add';
    
    if ($action === 'delete') {
        $courseId = (int) $_POST['course_id'];
        $pdo->prepare('DELETE FROM courses WHERE id = ?')->execute([$courseId]);
        audit_log(current_user()['id'], 'courses', $courseId, ['deleted' => true], null);
        flash('success', 'Course deleted.');
    } elseif ($action === 'edit') {
        $courseId = (int) $_POST['course_id'];
        $code = clean_string($_POST['code'] ?? '');
        $title = clean_string($_POST['title'] ?? '');
        $units = (int) $_POST['credit_units'];
        $level = clean_string($_POST['level'] ?? '');
        $semester = clean_string($_POST['semester'] ?? '');
        
        $pdo->prepare('UPDATE courses SET code = ?, title = ?, credit_units = ?, level = ?, semester = ? WHERE id = ?')
            ->execute([$code, $title, $units, $level, $semester, $courseId]);
            
        audit_log(current_user()['id'], 'courses', $courseId, null, ['action' => 'edit', 'code' => $code, 'title' => $title]);
        flash('success', 'Course updated.');
    } else {
        $pdo->prepare('INSERT INTO courses (code, title, credit_units, level, semester, status) VALUES (?, ?, ?, ?, ?, "active")')
            ->execute([clean_string($_POST['code'] ?? ''), clean_string($_POST['title'] ?? ''), (int) $_POST['credit_units'], clean_string($_POST['level'] ?? ''), clean_string($_POST['semester'] ?? '')]);
        $newId = (int) $pdo->lastInsertId();
        audit_log(current_user()['id'], 'courses', $newId, null, ['code' => $_POST['code'], 'title' => $_POST['title']]);
        
        // Notify all students
        $courseCode = clean_string($_POST['code'] ?? '');
        $courseTitle = clean_string($_POST['title'] ?? '');
        notify_role('student', 'New Course Added', "A new course ({$courseCode} - {$courseTitle}) has been added to the curriculum.");
        
        flash('success', 'Course added.');
    }
    redirect('admin/courses.php');
}
$courses = $pdo->query('SELECT * FROM courses ORDER BY level, code')->fetchAll();
$pageTitle = 'Courses';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace">
<div class="workspace-heading"><div><p class="eyebrow">Admin</p><h1>Course management</h1></div></div>
<form class="panel form-grid" method="post"><?= csrf_field() ?><label>Code <input name="code" required></label><label>Title <input name="title" required></label><label>Units <input type="number" name="credit_units" min="1" value="3"></label><label>Level <select name="level"><option>ND1</option><option>ND2</option><option>HND1</option><option>HND2</option></select></label><label>Semester <select name="semester"><option>First</option><option>Second</option></select></label><button class="cta-button" type="submit" style="align-self: end; justify-self: start;">Add course</button></form>
<section class="panel table-panel"><table><thead><tr><th>Code</th><th>Title</th><th>Units</th><th>Level</th><th>Semester</th><th>Action</th></tr></thead><tbody><?php foreach ($courses as $course): ?><tr><td><?= e($course['code']) ?></td><td><?= e($course['title']) ?></td><td><?= e((string) $course['credit_units']) ?></td><td><?= e($course['level']) ?></td><td><?= e($course['semester']) ?></td><td><div style="display: flex; gap: 8px;"><button type="button" class="button button-light" style="padding: 6px 12px; font-size: 13px;" onclick="editCourse(<?= htmlspecialchars(json_encode($course)) ?>)">Edit</button><form class="inline-form" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="course_id" value="<?= e((string) $course['id']) ?>"><button type="submit" class="button button-light" style="padding: 6px 12px; font-size: 13px; color: var(--danger); border-color: var(--danger);" onclick="return confirm('Are you sure you want to delete this course?');">Delete</button></form></div></td></tr><?php endforeach; ?></tbody></table></section>
</section></div>
<dialog id="editDialog" class="panel" style="padding: 32px; border: 1px solid var(--line); border-radius: var(--radius); width: 400px; max-width: 90vw; max-height: 90vh; top: 50%; left: 50%; transform: translate(-50%, -50%); margin: 0; background: var(--bg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">Edit Course</h3>
        <button type="button" onclick="document.getElementById('editDialog').close()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: var(--muted);">&times;</button>
    </div>
    <form method="post" style="display: grid; gap: 16px;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="course_id" id="edit_id">
        <label>Code <input name="code" id="edit_code" required></label>
        <label>Title <input name="title" id="edit_title" required></label>
        <label>Units <input type="number" name="credit_units" id="edit_units" min="1" required></label>
        <label>Level <select name="level" id="edit_level"><option>ND1</option><option>ND2</option><option>HND1</option><option>HND2</option></select></label>
        <label>Semester <select name="semester" id="edit_semester"><option>First</option><option>Second</option></select></label>
        <button type="submit" class="cta-button" style="margin-top: 16px;">Save Changes</button>
    </form>
</dialog>
<script>
function editCourse(course) {
    document.getElementById('edit_id').value = course.id;
    document.getElementById('edit_code').value = course.code;
    document.getElementById('edit_title').value = course.title;
    document.getElementById('edit_units').value = course.credit_units;
    document.getElementById('edit_level').value = course.level;
    document.getElementById('edit_semester').value = course.semester;
    document.getElementById('editDialog').showModal();
}
</script>
<?php include __DIR__ . '/../components/footer.php'; ?>
