<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../services/UserService.php';
$admin = require_auth(['admin']);
$role = in_array($_GET['role'] ?? 'student', ['student', 'staff'], true) ? $_GET['role'] : 'student';
$userService = new UserService($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = clean_string($_POST['action'] ?? '');
    $userId = (int) ($_POST['user_id'] ?? 0);
    
    if (in_array($action, ['active', 'inactive', 'suspended'], true)) {
        $pdo->prepare('UPDATE users SET status = ? WHERE id = ?')->execute([$action, $userId]);
        audit_log($admin['id'], 'users', $userId, ['action' => 'status_change'], ['status' => $action]);
        flash('success', 'User status updated.');
    } elseif ($action === 'delete') {
        $pdo->prepare('UPDATE users SET deleted_at = NOW(), status = "inactive" WHERE id = ?')->execute([$userId]);
        audit_log($admin['id'], 'users', $userId, ['action' => 'delete'], ['deleted' => true]);
        flash('success', 'User deleted.');
    } elseif ($action === 'add') {
        $email = clean_string($_POST['email'] ?? '');
        if ($userService->findByEmail($email)) {
            flash('error', 'A user with this email already exists.');
        } else {
            $newUserId = $userService->register(
                $role,
                clean_string($_POST['name'] ?? ''),
                $email,
                (string) ($_POST['password'] ?? ''),
                $_POST
            );
            $pdo->prepare('UPDATE users SET status = "active" WHERE id = ?')->execute([$newUserId]);
            audit_log($admin['id'], 'users', $newUserId, null, ['action' => 'add', 'role' => $role]);
            flash('success', ucfirst($role) . ' added successfully.');
        }
    } elseif ($action === 'edit') {
        $name = clean_string($_POST['name'] ?? '');
        $email = clean_string($_POST['email'] ?? '');
        $status = clean_string($_POST['status'] ?? '');
        
        $pdo->prepare('UPDATE users SET name = ?, email = ?, status = ? WHERE id = ?')
            ->execute([$name, $email, $status, $userId]);
            
        if ($role === 'student') {
            $pdo->prepare('UPDATE students SET matric_no = ?, level = ?, phone = ?, address = ? WHERE user_id = ?')
                ->execute([
                    clean_string($_POST['matric_no'] ?? ''),
                    clean_string($_POST['level'] ?? ''),
                    clean_string($_POST['phone'] ?? ''),
                    clean_string($_POST['address'] ?? ''),
                    $userId
                ]);
        } else {
            $pdo->prepare('UPDATE staff SET staff_no = ?, designation = ?, phone = ?, office = ? WHERE user_id = ?')
                ->execute([
                    clean_string($_POST['staff_no'] ?? ''),
                    clean_string($_POST['designation'] ?? ''),
                    clean_string($_POST['phone'] ?? ''),
                    clean_string($_POST['office'] ?? ''),
                    $userId
                ]);
        }
        
        if (!empty($_POST['password'])) {
            $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([password_hash($_POST['password'], PASSWORD_DEFAULT), $userId]);
        }
        
        audit_log($admin['id'], 'users', $userId, null, ['action' => 'edit']);
        flash('success', ucfirst($role) . ' updated successfully.');
    }
    redirect('admin/users.php?role=' . $role);
}

$search = clean_string($_GET['search'] ?? '');

if ($role === 'student') {
    $sql = 'SELECT users.*, roles.slug AS role, students.matric_no, students.level, students.phone, students.address 
            FROM users 
            INNER JOIN roles ON roles.id = users.role_id 
            LEFT JOIN students ON students.user_id = users.id 
            WHERE roles.slug = ? AND users.deleted_at IS NULL';
    $params = [$role];
    
    if ($search) {
        $sql .= ' AND (users.name LIKE ? OR users.email LIKE ? OR students.matric_no LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= ' ORDER BY users.created_at DESC';
    $statement = $pdo->prepare($sql);
} else {
    $sql = 'SELECT users.*, roles.slug AS role, staff.staff_no, staff.designation, staff.phone, staff.office 
            FROM users 
            INNER JOIN roles ON roles.id = users.role_id 
            LEFT JOIN staff ON staff.user_id = users.id 
            WHERE roles.slug = ? AND users.deleted_at IS NULL';
    $params = [$role];
    
    if ($search) {
        $sql .= ' AND (users.name LIKE ? OR users.email LIKE ? OR staff.staff_no LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= ' ORDER BY users.created_at DESC';
    $statement = $pdo->prepare($sql);
}
$statement->execute($params);
$users = $statement->fetchAll();
$pageTitle = ucfirst($role) . ' Management';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<style>
@media (min-width: 621px) {
    .form-grid.form-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .span-4 { grid-column: span 4; }
    .span-3 { grid-column: span 3; }
}
</style>
<div class="app-layout">
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<section class="workspace">
    <div class="workspace-heading"><div><p class="eyebrow">Admin</p><h1><?= e(ucfirst($role)) ?> management</h1></div></div>
    
    <form class="panel form-grid form-grid-4" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <label>Name <input name="name" required></label>
        <label>Email <input type="email" name="email" required></label>
        <label>Password <input type="password" name="password" required minlength="8"></label>
        <label>Phone <input name="phone"></label>
        <?php if ($role === 'student'): ?>
            <label>Matric number <input name="matric_no"></label>
            <label>Level <select name="level"><option>ND1</option><option>ND2</option><option>HND1</option><option>HND2</option></select></label>
            <label>Address <textarea name="address" rows="2"></textarea></label>
        <?php else: ?>
            <label>Staff number <input name="staff_no"></label>
            <label>Designation <input name="designation"></label>
            <label>Office <input name="office"></label>
        <?php endif; ?>
        <button class="cta-button" type="submit" style="align-self: end; justify-self: start;">Add <?= e(ucfirst($role)) ?></button>
    </form>
    
    <form class="panel" method="get" style="display: flex; gap: 12px; margin-bottom: 24px; padding: 16px; align-items: center;">
        <input type="hidden" name="role" value="<?= e($role) ?>">
        <input name="search" placeholder="Search by name, email or ID..." value="<?= e($search) ?>" style="flex: 1; margin: 0; max-width: 100%;">
        <button type="submit" class="button button-primary" style="margin: 0;">Search</button>
        <?php if ($search): ?>
            <a href="admin/users.php?role=<?= e($role) ?>" class="button button-light" style="margin: 0;">Clear</a>
        <?php endif; ?>
    </form>
    <section class="panel table-panel">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Joined</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($users as $row): ?>
                <tr>
                    <td><?= e($row['name']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td><span class="status"><?= e($row['status']) ?></span></td>
                    <td><?= e(date('M d, Y', strtotime($row['created_at']))) ?></td>
                    <td>
                        <div style="display: flex; gap: 8px; width: 100%; justify-content: space-between; align-items: center;">
                            <form class="inline-form" method="post" style="margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="user_id" value="<?= e((string) $row['id']) ?>">
                                <select name="action">
                                    <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Activate</option>
                                    <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Deactivate</option>
                                    <option value="suspended" <?= $row['status'] === 'suspended' ? 'selected' : '' ?>>Suspend</option>
                                </select>
                                <button class="button button-light" type="submit" style="padding: 6px 12px; font-size: 13px;">Apply</button>
                            </form>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" class="button button-light" style="padding: 6px 12px; font-size: 13px;" onclick="editUser(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                                <form class="inline-form" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');" style="margin: 0;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?= e((string) $row['id']) ?>">
                                    <button type="submit" class="button button-light" style="padding: 6px 12px; font-size: 13px; color: var(--danger); border-color: var(--danger);">Delete</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>
</div>

<dialog id="editDialog" class="panel" style="padding: 32px; border: 1px solid var(--line); border-radius: var(--radius); width: 800px; max-width: 95vw; max-height: 90vh; top: 50%; left: 50%; transform: translate(-50%, -50%); margin: 0; background: var(--bg); overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">Edit <?= e(ucfirst($role)) ?></h3>
        <button type="button" onclick="document.getElementById('editDialog').close()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: var(--muted);">&times;</button>
    </div>
    <form method="post" class="form-grid form-grid-4">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="user_id" id="edit_id">
        <label>Name <input name="name" id="edit_name" required></label>
        <label>Email <input type="email" name="email" id="edit_email" required></label>
        <label>Password <input type="password" name="password" placeholder="Leave blank to keep current" minlength="8"></label>
        <label>Phone <input name="phone" id="edit_phone"></label>
        <label>Status
            <select name="status" id="edit_status">
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>
        </label>
        <?php if ($role === 'student'): ?>
            <label>Matric number <input name="matric_no" id="edit_matric_no"></label>
            <label>Level <select name="level" id="edit_level"><option>ND1</option><option>ND2</option><option>HND1</option><option>HND2</option></select></label>
            <label>Address <textarea name="address" id="edit_address" rows="2"></textarea></label>
        <?php else: ?>
            <label>Staff number <input name="staff_no" id="edit_staff_no"></label>
            <label>Designation <input name="designation" id="edit_designation"></label>
            <label>Office <input name="office" id="edit_office"></label>
        <?php endif; ?>
        <button type="submit" class="cta-button span-4" style="margin-top: 16px;">Save Changes</button>
    </form>
</dialog>

<script>
function editUser(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_phone').value = user.phone || '';
    document.getElementById('edit_status').value = user.status;
    <?php if ($role === 'student'): ?>
        document.getElementById('edit_matric_no').value = user.matric_no || '';
        document.getElementById('edit_level').value = user.level || 'ND1';
        document.getElementById('edit_address').value = user.address || '';
    <?php else: ?>
        document.getElementById('edit_staff_no').value = user.staff_no || '';
        document.getElementById('edit_designation').value = user.designation || '';
        document.getElementById('edit_office').value = user.office || '';
    <?php endif; ?>
    document.getElementById('editDialog').showModal();
}
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>
