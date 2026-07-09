<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'add';
    
    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM announcements WHERE id = ?')->execute([(int) $_POST['announcement_id']]);
        flash('success', 'Announcement deleted.');
    } else {
        $title = clean_string($_POST['title'] ?? '');
        $body = clean_string($_POST['body'] ?? '');
        $status = in_array($_POST['status'], ['draft', 'published', 'archived']) ? $_POST['status'] : 'draft';
        $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : null;
        
        $pdo->prepare('INSERT INTO announcements (title, body, status, published_at, created_by) VALUES (?, ?, ?, ?, ?)')
            ->execute([$title, $body, $status, $publishedAt, current_user()['id']]);
            
        $announcementId = $pdo->lastInsertId();
        
        if (!empty($_POST['roles']) && is_array($_POST['roles'])) {
            $stmt = $pdo->prepare('INSERT INTO announcement_roles (announcement_id, role_id) VALUES (?, ?)');
            foreach ($_POST['roles'] as $roleId) {
                $stmt->execute([$announcementId, (int) $roleId]);
            }
        }
        
        flash('success', 'Announcement created.');
    }
    redirect('admin/announcements.php');
}

$roles = $pdo->query('SELECT * FROM roles')->fetchAll();
$announcements = $pdo->query('
    SELECT announcements.*, users.name as author, GROUP_CONCAT(roles.name SEPARATOR ", ") as audience
    FROM announcements 
    LEFT JOIN users ON users.id = announcements.created_by
    LEFT JOIN announcement_roles ON announcement_roles.announcement_id = announcements.id
    LEFT JOIN roles ON roles.id = announcement_roles.role_id
    GROUP BY announcements.id
    ORDER BY created_at DESC
')->fetchAll();

$pageTitle = 'Manage Announcements';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Admin</p>
                <h1>Manage announcements</h1>
            </div>
        </div>
        
        <form class="panel form-grid" method="post" style="grid-template-columns: 1fr;">
            <?= csrf_field() ?>
            <label>Title <input name="title" required></label>
            <label>Message <textarea name="body" rows="6" required></textarea></label>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <label>Status 
                    <select name="status">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </label>
                
                <div>
                    <span style="display: block; margin-bottom: 12px; font-weight: 500; color: var(--ink); font-size: 15px;">Target Audience</span>
                    <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                        <?php foreach ($roles as $role): ?>
                            <label style="display: flex; flex-direction: row; align-items: center; gap: 8px; font-weight: 400; font-size: 16px; color: var(--text);">
                                <input type="checkbox" name="roles[]" value="<?= e((string) $role['id']) ?>" checked>
                                <?= e($role['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <button class="cta-button" type="submit" style="justify-self: start; margin-top: 12px;">Post announcement</button>
        </form>
        
        <section class="panel table-panel">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Audience</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $ann): ?>
                        <tr>
                            <td>
                                <strong><?= e($ann['title']) ?></strong><br>
                                <small style="color: var(--text-muted);"><?= e(substr($ann['body'], 0, 60)) ?>...</small>
                            </td>
                            <td><?= e($ann['audience'] ?: 'Everyone') ?></td>
                            <td><?= e(ucfirst($ann['status'])) ?></td>
                            <td><?= e(date('M d, Y', strtotime($ann['created_at']))) ?></td>
                            <td>
                                <form class="inline-form" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="announcement_id" value="<?= e((string) $ann['id']) ?>">
                                    <button type="submit" class="button button-light" onclick="return confirm('Delete this announcement?');">Delete</button>
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
