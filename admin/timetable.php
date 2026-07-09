<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['admin']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'add';
    
    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM timetable WHERE id = ?')->execute([(int) $_POST['slot_id']]);
        flash('success', 'Timetable slot deleted.');
    } elseif ($action === 'edit') {
        $slotId = (int) $_POST['slot_id'];
        $courseId = (int) $_POST['course_id'];
        $staffId = (int) $_POST['staff_id'];
        
        $pdo->prepare('UPDATE timetable SET course_id = ?, staff_id = ?, day_of_week = ?, start_time = ?, end_time = ?, venue = ?, level = ?, semester = ? WHERE id = ?')
            ->execute([$courseId, $staffId, clean_string($_POST['day_of_week'] ?? ''), $_POST['start_time'], $_POST['end_time'], clean_string($_POST['venue'] ?? ''), clean_string($_POST['level'] ?? ''), clean_string($_POST['semester'] ?? ''), $slotId]);
            
        flash('success', 'Timetable slot updated.');
    } else {
        $courseId = (int) $_POST['course_id'];
        $staffId = (int) $_POST['staff_id'];
        
        $pdo->prepare('INSERT INTO timetable (course_id, staff_id, day_of_week, start_time, end_time, venue, level, semester) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
            ->execute([$courseId, $staffId, clean_string($_POST['day_of_week'] ?? ''), $_POST['start_time'], $_POST['end_time'], clean_string($_POST['venue'] ?? ''), clean_string($_POST['level'] ?? ''), clean_string($_POST['semester'] ?? '')]);
            
        // Find staff user ID
        $stmt = $pdo->prepare('SELECT user_id FROM staff WHERE id = ?');
        $stmt->execute([$staffId]);
        $staffUserId = $stmt->fetchColumn();
        
        if ($staffUserId) {
            // Get course code
            $stmt = $pdo->prepare('SELECT code FROM courses WHERE id = ?');
            $stmt->execute([$courseId]);
            $courseCode = $stmt->fetchColumn();
            
            $day = clean_string($_POST['day_of_week'] ?? '');
            notify_user((int)$staffUserId, 'New Class Assigned', "You have been assigned to teach {$courseCode} on {$day}.");
        }
            
        flash('success', 'Timetable slot added.');
    }
    redirect('admin/timetable.php');
}
$courses = $pdo->query('SELECT id, code, title FROM courses ORDER BY code')->fetchAll();
$staff = $pdo->query('SELECT staff.id, users.name FROM staff INNER JOIN users ON users.id = staff.user_id ORDER BY users.name')->fetchAll();
$slots = $pdo->query('SELECT timetable.*, courses.code, users.name AS lecturer FROM timetable INNER JOIN courses ON courses.id = timetable.course_id LEFT JOIN staff ON staff.id = timetable.staff_id LEFT JOIN users ON users.id = staff.user_id ORDER BY day_of_week, start_time')->fetchAll();
$pageTitle = 'Timetable';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace">
<div class="workspace-heading"><div><p class="eyebrow">Admin</p><h1>Timetable builder</h1></div></div>
<form class="panel form-grid" method="post"><?= csrf_field() ?><label>Course <select name="course_id"><?php foreach ($courses as $course): ?><option value="<?= e((string) $course['id']) ?>"><?= e($course['code']) ?> - <?= e($course['title']) ?></option><?php endforeach; ?></select></label><label>Lecturer <select name="staff_id"><?php foreach ($staff as $row): ?><option value="<?= e((string) $row['id']) ?>"><?= e($row['name']) ?></option><?php endforeach; ?></select></label><label>Day <select name="day_of_week"><option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option></select></label><label>Start <input type="time" name="start_time" required></label><label>End <input type="time" name="end_time" required></label><label>Venue <input name="venue" required></label><label>Level <select name="level"><option>ND1</option><option>ND2</option><option>HND1</option><option>HND2</option></select></label><label>Semester <select name="semester"><option>First</option><option>Second</option></select></label><button class="cta-button" type="submit" style="align-self: end; justify-self: start;">Add slot</button></form>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; margin-top: 24px;">
    <h2 style="margin: 0; font-size: 18px;">Manage Slots</h2>
    <div style="display: flex; gap: 8px;">
        <button type="button" class="cta-button" onclick="document.getElementById('kanbanView').style.display='block'; document.getElementById('tableView').style.display='none';" style="display: inline-flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="9" y1="3" x2="9" y2="21"></line>
                <line x1="15" y1="3" x2="15" y2="21"></line>
            </svg>
            Kanban View
        </button>
        <button type="button" class="cta-button" onclick="document.getElementById('kanbanView').style.display='none'; document.getElementById('tableView').style.display='block';" style="background: var(--surface); color: var(--text); border: 1px solid var(--line); display: inline-flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="8" y1="6" x2="21" y2="6"></line>
                <line x1="8" y1="12" x2="21" y2="12"></line>
                <line x1="8" y1="18" x2="21" y2="18"></line>
                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                <line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
            Table View
        </button>
    </div>
</div>

<div id="kanbanView">
<section style="background: var(--bg); border: 1px solid var(--line); border-radius: 12px; padding: 16px;">
    <div class="timetable-grid" style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; min-height: 60vh;">
        <?php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; ?>
        <?php foreach ($days as $day): ?>
        <div class="timetable-day" data-day="<?= $day ?>" style="background: var(--surface); border-radius: 8px; padding: 12px; display: flex; flex-direction: column; gap: 12px; border: 1px solid var(--line); transition: all 0.2s ease;">
            <h3 style="text-align: center; font-size: 14px; text-transform: uppercase; border-bottom: 1px solid var(--line); padding-bottom: 8px; margin-top: 0; margin-bottom: 8px;"><?= $day ?></h3>
            <?php foreach ($slots as $slot): ?>
                <?php if ($slot['day_of_week'] === $day): ?>
                    <article class="timetable-slot" draggable="true" data-id="<?= e((string) $slot['id']) ?>" style="position: relative; background: var(--bg); padding: 12px; border-radius: 6px; border: 1px solid var(--line); cursor: grab; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <strong style="display: block; font-size: 14px; margin-bottom: 4px;"><?= e($slot['code']) ?></strong>
                        <span style="display: block; font-size: 12px; color: var(--muted); margin-bottom: 4px;"><?= e(substr($slot['start_time'], 0, 5)) ?> - <?= e(substr($slot['end_time'], 0, 5)) ?></span>
                        <small style="display: block; font-size: 11px; color: var(--muted);"><?= e($slot['lecturer'] ?? 'Unassigned') ?> &middot; <?= e($slot['venue']) ?></small>
                        <div style="position: absolute; top: 8px; right: 8px; display: flex; gap: 4px;">
                            <button type="button" style="background: none; border: none; font-size: 14px; cursor: pointer; color: var(--accent); padding: 4px;" onclick="openEditModal(<?= e((string) $slot['id']) ?>, <?= e((string) $slot['course_id']) ?>, <?= e((string) $slot['staff_id']) ?>, '<?= e($slot['day_of_week']) ?>', '<?= e(substr($slot['start_time'], 0, 5)) ?>', '<?= e(substr($slot['end_time'], 0, 5)) ?>', '<?= e($slot['venue']) ?>', '<?= e($slot['level']) ?>', '<?= e($slot['semester']) ?>')">
                                ✎
                            </button>
                            <form class="inline-form" method="post" style="margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="slot_id" value="<?= e((string) $slot['id']) ?>">
                                <button type="submit" style="background: none; border: none; font-size: 16px; cursor: pointer; color: var(--danger); padding: 4px;" onclick="return confirm('Delete this slot?');">&times;</button>
                            </form>
                        </div>
                    </article>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>
</div>

<div id="tableView" style="display: none;">
    <div class="panel">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Lecturer</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Venue</th>
                        <th>Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $slot): ?>
                        <tr>
                            <td><?= e($slot['code']) ?></td>
                            <td><?= e($slot['lecturer'] ?? 'Unassigned') ?></td>
                            <td><?= e($slot['day_of_week']) ?></td>
                            <td><?= e(substr($slot['start_time'], 0, 5)) ?> - <?= e(substr($slot['end_time'], 0, 5)) ?></td>
                            <td><?= e($slot['venue']) ?></td>
                            <td><?= e($slot['level']) ?></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <button type="button" class="button button-light" style="padding: 6px 12px; font-size: 13px;" onclick="openEditModal(<?= e((string) $slot['id']) ?>, <?= e((string) $slot['course_id']) ?>, <?= e((string) $slot['staff_id']) ?>, '<?= e($slot['day_of_week']) ?>', '<?= e(substr($slot['start_time'], 0, 5)) ?>', '<?= e(substr($slot['end_time'], 0, 5)) ?>', '<?= e($slot['venue']) ?>', '<?= e($slot['level']) ?>', '<?= e($slot['semester']) ?>')">Edit</button>
                                    <form method="post" style="margin: 0; display: inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="slot_id" value="<?= e((string) $slot['id']) ?>">
                                        <button type="submit" class="button button-light" style="padding: 6px 12px; font-size: 13px; color: var(--danger); border-color: var(--danger);" onclick="return confirm('Delete this slot?');">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</section></div>

<dialog id="editSlotModal" class="panel" style="padding: 32px; border: 1px solid var(--line); border-radius: var(--radius); width: 600px; max-width: 90vw; max-height: 90vh; margin: auto; background: var(--bg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0;">Edit Timetable Slot</h3>
        <button type="button" onclick="this.closest('dialog').close()" style="background:none; border:none; font-size: 20px; cursor: pointer;">&times;</button>
    </div>
    <form class="form-grid" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="slot_id" id="edit_slot_id">
        <label>Course <select name="course_id" id="edit_course_id"><?php foreach ($courses as $course): ?><option value="<?= e((string) $course['id']) ?>"><?= e($course['code']) ?> - <?= e($course['title']) ?></option><?php endforeach; ?></select></label>
        <label>Lecturer <select name="staff_id" id="edit_staff_id"><?php foreach ($staff as $row): ?><option value="<?= e((string) $row['id']) ?>"><?= e($row['name']) ?></option><?php endforeach; ?></select></label>
        <label>Day <select name="day_of_week" id="edit_day_of_week"><option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option></select></label>
        <label>Start <input type="time" name="start_time" id="edit_start_time" required></label>
        <label>End <input type="time" name="end_time" id="edit_end_time" required></label>
        <label>Venue <input name="venue" id="edit_venue" required></label>
        <label>Level <select name="level" id="edit_level"><option>ND1</option><option>ND2</option><option>HND1</option><option>HND2</option></select></label>
        <label>Semester <select name="semester" id="edit_semester"><option>First</option><option>Second</option></select></label>
        <button class="cta-button" type="submit" style="align-self: end; justify-self: start;">Save Changes</button>
    </form>
</dialog>

<script>
function openEditModal(id, course, staff, day, start, end, venue, level, semester) {
    document.getElementById('edit_slot_id').value = id;
    document.getElementById('edit_course_id').value = course;
    document.getElementById('edit_staff_id').value = staff;
    document.getElementById('edit_day_of_week').value = day;
    document.getElementById('edit_start_time').value = start;
    document.getElementById('edit_end_time').value = end;
    document.getElementById('edit_venue').value = venue;
    document.getElementById('edit_level').value = level;
    document.getElementById('edit_semester').value = semester;
    document.getElementById('editSlotModal').showModal();
}

document.addEventListener('DOMContentLoaded', () => {
    const slots = document.querySelectorAll('.timetable-slot');
    const columns = document.querySelectorAll('.timetable-day');
    let draggedSlot = null;

    slots.forEach(slot => {
        slot.addEventListener('dragstart', (e) => {
            draggedSlot = slot;
            setTimeout(() => slot.style.opacity = '0.5', 0);
        });
        slot.addEventListener('dragend', () => {
            setTimeout(() => {
                if (draggedSlot) draggedSlot.style.opacity = '1';
                draggedSlot = null;
            }, 0);
            columns.forEach(col => col.style.borderColor = 'var(--line)');
        });
    });

    columns.forEach(col => {
        col.addEventListener('dragover', (e) => {
            e.preventDefault();
            col.style.borderColor = 'var(--accent)';
        });
        col.addEventListener('dragleave', () => {
            col.style.borderColor = 'var(--line)';
        });
        col.addEventListener('drop', (e) => {
            e.preventDefault();
            col.style.borderColor = 'var(--line)';
            if (draggedSlot && draggedSlot.parentElement !== col) {
                col.appendChild(draggedSlot);
                const slotId = draggedSlot.getAttribute('data-id');
                const newDay = col.getAttribute('data-day');
                
                fetch('<?= url('api/update_timetable_slot.php') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ slot_id: slotId, new_day: newDay })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert('Error saving timetable change.');
                        window.location.reload();
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Network error. Please try again.');
                    window.location.reload();
                });
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>
