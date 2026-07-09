<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'add';
    
    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM document_requirements WHERE id = ?')->execute([(int) $_POST['requirement_id']]);
        flash('success', 'Document requirement deleted.');
    } else {
        $name = clean_string($_POST['name'] ?? '');
        $description = clean_string($_POST['description'] ?? '');
        $audience = in_array($_POST['audience'], ['student', 'staff', 'all']) ? $_POST['audience'] : 'all';
        $allowed = clean_string($_POST['allowed_extensions'] ?? 'jpg,jpeg,png,pdf');
        $maxSize = (int) ($_POST['max_size_mb'] ?? 2);
        $isRequired = isset($_POST['is_required']) ? 1 : 0;
        
        $pdo->prepare('INSERT INTO document_requirements (name, description, audience, allowed_extensions, max_size_mb, is_required) VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$name, $description, $audience, $allowed, $maxSize, $isRequired]);
            
        flash('success', 'Document requirement added.');
    }
    redirect('admin/document_requirements.php');
}

$requirements = $pdo->query('SELECT * FROM document_requirements ORDER BY audience, name')->fetchAll();

$pageTitle = 'Document Requirements';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Settings</p>
                <h1>Document requirements</h1>
            </div>
            <div class="workspace-actions">
                <a href="<?= url('admin/settings.php') ?>" class="button button-light">Back to Settings</a>
            </div>
        </div>
        
        <form class="panel form-grid" method="post">
            <?= csrf_field() ?>
            <label>Name <input name="name" required placeholder="e.g. O-Level Result"></label>
            <label>Audience 
                <select name="audience">
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                    <option value="all">All</option>
                </select>
            </label>
            <label>Allowed Extensions <input name="allowed_extensions" value="jpg,jpeg,png,pdf"></label>
            <label>Max Size (MB) <input type="number" name="max_size_mb" value="2" min="1"></label>
            <label style="grid-column: 1 / -1;">Description (Optional) <input name="description"></label>
            <label style="grid-column: 1 / -1; display: flex; align-items: center; gap: 8px; font-weight: normal;">
                <input type="checkbox" name="is_required" checked> This document is mandatory
            </label>
            <button class="cta-button" type="submit" style="grid-column: 1 / -1; justify-self: start; margin-top: 8px;">Add requirement</button>
        </form>
        
        <section class="panel table-panel">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Audience</th>
                        <th>File Types</th>
                        <th>Size Limit</th>
                        <th>Required</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requirements as $req): ?>
                        <tr>
                            <td>
                                <strong><?= e($req['name']) ?></strong>
                                <?php if ($req['description']): ?>
                                    <br><small style="color: var(--text-muted);"><?= e($req['description']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= e(ucfirst($req['audience'])) ?></td>
                            <td><span class="status"><?= e($req['allowed_extensions']) ?></span></td>
                            <td><?= e((string) $req['max_size_mb']) ?> MB</td>
                            <td><?= $req['is_required'] ? 'Yes' : 'No' ?></td>
                            <td>
                                <form class="inline-form" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="requirement_id" value="<?= e((string) $req['id']) ?>">
                                    <button type="submit" class="button button-light" onclick="return confirm('Delete this requirement?');">Delete</button>
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
