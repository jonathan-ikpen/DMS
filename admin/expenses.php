<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['admin']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'add';
    
    if ($action === 'delete') {
        $expenseId = (int) $_POST['expense_id'];
        $pdo->prepare('DELETE FROM expenses WHERE id = ?')->execute([$expenseId]);
        audit_log(current_user()['id'], 'expenses', $expenseId, ['deleted' => true], null);
        flash('success', 'Expense deleted.');
    } elseif ($action === 'edit') {
        $expenseId = (int) $_POST['expense_id'];
        $categoryId = (int) $_POST['category_id'];
        $title = clean_string($_POST['title'] ?? '');
        $amount = (float) $_POST['amount'];
        $expenseDate = $_POST['expense_date'];
        
        $pdo->prepare('UPDATE expenses SET category_id = ?, title = ?, amount = ?, expense_date = ? WHERE id = ?')
            ->execute([$categoryId, $title, $amount, $expenseDate, $expenseId]);
            
        audit_log(current_user()['id'], 'expenses', $expenseId, null, ['action' => 'edit', 'title' => $title, 'amount' => $amount]);
        flash('success', 'Expense updated.');
    } else {
        $pdo->prepare('INSERT INTO expenses (category_id, title, amount, expense_date, status, created_by) VALUES (?, ?, ?, ?, "approved", ?)')
            ->execute([(int) $_POST['category_id'], clean_string($_POST['title'] ?? ''), (float) $_POST['amount'], $_POST['expense_date'], current_user()['id']]);
        $newId = (int) $pdo->lastInsertId();
        audit_log(current_user()['id'], 'expenses', $newId, null, ['title' => $_POST['title'], 'amount' => $_POST['amount']]);
        flash('success', 'Expense recorded.');
    }
    redirect('admin/expenses.php');
}
$categories = $pdo->query('SELECT * FROM expense_categories ORDER BY name')->fetchAll();
$expenses = $pdo->query('SELECT expenses.*, expense_categories.name AS category FROM expenses INNER JOIN expense_categories ON expense_categories.id = expenses.category_id ORDER BY expense_date DESC')->fetchAll();
$pageTitle = 'Expenses';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace"><div class="workspace-heading"><div><p class="eyebrow">Admin</p><h1>Expenses</h1></div></div><form class="panel form-grid" method="post"><?= csrf_field() ?><label>Category <select name="category_id"><?php foreach ($categories as $category): ?><option value="<?= e((string) $category['id']) ?>"><?= e($category['name']) ?></option><?php endforeach; ?></select></label><label>Title <input name="title" required></label><label>Amount <input type="number" step="0.01" name="amount" required></label><label>Date <input type="date" name="expense_date" required></label><button class="cta-button" type="submit" style="align-self: end; justify-self: start;">Record expense</button></form><section class="panel table-panel"><table><thead><tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach ($expenses as $expense): ?><tr><td><?= e($expense['title']) ?></td><td><?= e($expense['category']) ?></td><td><?= e(money($expense['amount'])) ?></td><td><?= e($expense['expense_date']) ?></td><td><?= e($expense['status']) ?></td><td><div style="display: flex; gap: 8px;"><button type="button" class="button button-light" style="padding: 6px 12px; font-size: 13px;" onclick="editExpense(<?= htmlspecialchars(json_encode($expense)) ?>)">Edit</button><form class="inline-form" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="expense_id" value="<?= e((string) $expense['id']) ?>"><button type="submit" class="button button-light" style="padding: 6px 12px; font-size: 13px; color: var(--danger); border-color: var(--danger);" onclick="return confirm('Are you sure you want to delete this expense?');">Delete</button></form></div></td></tr><?php endforeach; ?></tbody></table></section></section></div>
<dialog id="editDialog" class="panel" style="padding: 32px; border: 1px solid var(--line); border-radius: var(--radius); width: 400px; max-width: 90vw; max-height: 90vh; top: 50%; left: 50%; transform: translate(-50%, -50%); margin: 0; background: var(--bg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">Edit Expense</h3>
        <button type="button" onclick="document.getElementById('editDialog').close()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: var(--muted);">&times;</button>
    </div>
    <form method="post" style="display: grid; gap: 16px;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="expense_id" id="edit_id">
        <label>Category <select name="category_id" id="edit_category_id"><?php foreach ($categories as $category): ?><option value="<?= e((string) $category['id']) ?>"><?= e($category['name']) ?></option><?php endforeach; ?></select></label>
        <label>Title <input name="title" id="edit_title" required></label>
        <label>Amount <input type="number" step="0.01" name="amount" id="edit_amount" required></label>
        <label>Date <input type="date" name="expense_date" id="edit_expense_date" required></label>
        <button type="submit" class="cta-button" style="margin-top: 16px;">Save Changes</button>
    </form>
</dialog>
<script>
function editExpense(expense) {
    document.getElementById('edit_id').value = expense.id;
    document.getElementById('edit_category_id').value = expense.category_id;
    document.getElementById('edit_title').value = expense.title;
    document.getElementById('edit_amount').value = expense.amount;
    document.getElementById('edit_expense_date').value = expense.expense_date;
    document.getElementById('editDialog').showModal();
}
</script><?php include __DIR__ . '/../components/footer.php'; ?>
