<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $uploadId = (int) ($_POST['upload_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    if (in_array($action, ['approved', 'rejected', 'pending'])) {
        $stmt = $pdo->prepare('UPDATE document_uploads SET status = ? WHERE id = ?');
        if ($stmt->execute([$action, $uploadId])) {
            $msgAction = $action === 'pending' ? 'revoked and reset to pending' : $action;
            flash('success', "Document has been $msgAction successfully.");
        } else {
            flash('error', 'Failed to update document status.');
        }
    }
    
    // Redirect with the same GET parameters so the admin doesn't lose their search context
    $queryString = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    redirect('admin/document_reviews.php' . $queryString);
}

// Build query based on filters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'all';
$roleFilter = $_GET['role'] ?? 'all';

$whereClause = "WHERE 1=1";
$params = [];

if ($status !== 'all') {
    $whereClause .= " AND u.status = ?";
    $params[] = $status;
}

if ($roleFilter !== 'all') {
    // Determine the role slug based on selection
    if ($roleFilter === 'student' || $roleFilter === 'staff') {
        $whereClause .= " AND roles.slug = ?";
        $params[] = $roleFilter;
    }
}

if (!empty($search)) {
    $whereClause .= " AND (usr.name LIKE ? OR usr.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query = "
    SELECT u.id as upload_id, u.file_path, u.status, u.created_at,
           r.name as requirement_name,
           usr.name as user_name, usr.email, roles.name as role_name
    FROM document_uploads u
    JOIN document_requirements r ON u.requirement_id = r.id
    JOIN users usr ON u.user_id = usr.id
    JOIN roles ON usr.role_id = roles.id
    $whereClause
    ORDER BY u.created_at DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$uploads = $stmt->fetchAll();

$pageTitle = 'Document Reviews';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Admin</p>
                <h1>Document Reviews</h1>
            </div>
        </div>
        
        <section class="panel" style="margin-bottom: 24px;">
            <form method="get" class="form-grid" style="grid-template-columns: 1fr auto auto auto; align-items: end;">
                <label>Search User
                    <input type="text" name="search" value="<?= e($search) ?>" placeholder="Name or email...">
                </label>
                <label>Role
                    <select name="role">
                        <option value="all" <?= $roleFilter === 'all' ? 'selected' : '' ?>>All Roles</option>
                        <option value="student" <?= $roleFilter === 'student' ? 'selected' : '' ?>>Students</option>
                        <option value="staff" <?= $roleFilter === 'staff' ? 'selected' : '' ?>>Staff</option>
                    </select>
                </label>
                <label>Status
                    <select name="status">
                        <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </label>
                <button type="submit" class="cta-button" style="margin-bottom: 2px;">Filter</button>
            </form>
        </section>
        
        <section class="panel table-panel">
            <?php if (empty($uploads)): ?>
                <p style="padding: 24px; color: var(--muted); text-align: center;">No documents match your criteria.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Requirement</th>
                            <th>Date Uploaded</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($uploads as $upload): ?>
                            <tr>
                                <td>
                                    <strong><?= e($upload['user_name']) ?></strong><br>
                                    <small style="color: var(--muted);"><?= e($upload['email']) ?></small>
                                </td>
                                <td><?= e($upload['role_name']) ?></td>
                                <td><?= e($upload['requirement_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($upload['created_at'])) ?></td>
                                <td><span class="status"><?= ucfirst(e($upload['status'])) ?></span></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="<?= url('download.php?file=' . e($upload['file_path'])) ?>" target="_blank" class="button button-light" style="padding: 6px 12px; font-size: 13px;">View</a>
                                        
                                        <?php if ($upload['status'] === 'pending'): ?>
                                            <form method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="upload_id" value="<?= $upload['upload_id'] ?>">
                                                <input type="hidden" name="action" value="approved">
                                                <button type="submit" class="cta-button" style="padding: 6px 12px; font-size: 13px; border-radius: var(--radius);">Approve</button>
                                            </form>
                                            <form method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="upload_id" value="<?= $upload['upload_id'] ?>">
                                                <input type="hidden" name="action" value="rejected">
                                                <button type="submit" class="button button-light" style="padding: 6px 12px; font-size: 13px; color: var(--danger); border-color: var(--danger);">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" onsubmit="return confirm('Are you sure you want to revoke this status? It will require review again.');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="upload_id" value="<?= $upload['upload_id'] ?>">
                                                <input type="hidden" name="action" value="pending">
                                                <button type="submit" class="button button-light" style="padding: 6px 12px; font-size: 13px;">Revoke</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
