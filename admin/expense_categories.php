<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'add';
    
    if ($action === 'delete') {
        // Prevent deleting system categories (id 1,2,3 for example, or is_system = 1)
        $pdo->prepare('DELETE FROM expense_categories WHERE id = ? AND is_system = 0')->execute([(int) $_POST['category_id']]);
        flash('success', 'Expense category deleted.');
    } elseif ($action === 'edit') {
        $categoryId = (int) $_POST['category_id'];
        $name = clean_string($_POST['name'] ?? '');
        try {
            $pdo->prepare('UPDATE expense_categories SET name = ? WHERE id = ? AND is_system = 0')->execute([$name, $categoryId]);
            flash('success', 'Expense category updated.');
        } catch (PDOException $e) {
            flash('error', 'Category name already exists.');
        }
    } else {
        $name = clean_string($_POST['name'] ?? '');
        try {
            $pdo->prepare('INSERT INTO expense_categories (name, is_system) VALUES (?, 0)')->execute([$name]);
            flash('success', 'Expense category added.');
        } catch (PDOException $e) {
            flash('error', 'Category name already exists.');
        }
    }
    redirect('admin/expense_categories.php');
}

$categories = $pdo->query('SELECT * FROM expense_categories ORDER BY name')->fetchAll();

$pageTitle = 'Expense Categories';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Settings</p>
                <h1>Expense categories</h1>
            </div>
            <div class="workspace-actions">
                <a href="<?= url('admin/settings.php') ?>" class="button button-light">Back to Settings</a>
            </div>
        </div>
        
        <form class="panel form-grid" method="post">
            <?= csrf_field() ?>
            <label>Category Name <input name="name" required placeholder="e.g. Server Maintenance"></label>
            <button class="cta-button" type="submit" style="align-self: end; justify-self: start;">Add category</button>
        </form>
        
        <section class="panel table-panel">
            <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><strong><?= e($cat['name']) ?></strong></td>
                            <td><?= $cat['is_system'] ? '<span class="status">System</span>' : 'Custom' ?></td>
                            <td>
                                <?php if (!$cat['is_system']): ?>
                                <div style="display: flex; gap: 8px;">
                                    <button type="button" class="button button-light" style="padding: 6px 12px; font-size: 13px;" onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)">Edit</button>
                                    <form class="inline-form" method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="category_id" value="<?= e((string) $cat['id']) ?>">
                                        <button type="submit" class="button button-light" style="padding: 6px 12px; font-size: 13px; color: var(--danger); border-color: var(--danger);" onclick="return confirm('Delete this category?');">Delete</button>
                                    </form>
                                </div>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.875rem;">Cannot modify</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </section>
</div>

<dialog id="editDialog" class="panel" style="padding: 32px; border: 1px solid var(--line); border-radius: var(--radius); width: 400px; max-width: 90vw; max-height: 90vh; top: 50%; left: 50%; transform: translate(-50%, -50%); margin: 0; background: var(--bg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">Edit Category</h3>
        <button type="button" onclick="document.getElementById('editDialog').close()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: var(--muted);">&times;</button>
    </div>
    <form method="post" style="display: grid; gap: 16px;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="category_id" id="edit_id">
        <label>Category Name <input name="name" id="edit_name" required></label>
        <button type="submit" class="cta-button" style="margin-top: 16px;">Save Changes</button>
    </form>
</dialog>
<script>
function editCategory(cat) {
    document.getElementById('edit_id').value = cat.id;
    document.getElementById('edit_name').value = cat.name;
    document.getElementById('editDialog').showModal();
}
</script>
<?php include __DIR__ . '/../components/footer.php'; ?>
