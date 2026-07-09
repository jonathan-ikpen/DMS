<?php
require_once __DIR__ . '/../middleware/auth.php';
$admin = require_auth(['admin']);
$role = in_array($_GET['role'] ?? 'student', ['student', 'staff'], true) ? $_GET['role'] : 'student';

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
    }
    redirect('admin/users.php?role=' . $role);
}

$statement = $pdo->prepare(
    'SELECT users.*, roles.slug AS role FROM users INNER JOIN roles ON roles.id = users.role_id WHERE roles.slug = ? AND users.deleted_at IS NULL ORDER BY users.created_at DESC'
);
$statement->execute([$role]);
$users = $statement->fetchAll();
$pageTitle = ucfirst($role) . ' Management';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<section class="workspace">
    <div class="workspace-heading"><div><p class="eyebrow">Admin</p><h1><?= e(ucfirst($role)) ?> management</h1></div></div>
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
                        <form class="inline-form" method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="user_id" value="<?= e((string) $row['id']) ?>">
                            <select name="action">
                                <option value="active">Activate</option>
                                <option value="inactive">Deactivate</option>
                                <option value="suspended">Suspend</option>
                                <option value="delete">Delete</option>
                            </select>
                            <button class="button button-light" type="submit">Apply</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
