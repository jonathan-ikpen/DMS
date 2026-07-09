<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $amount = (float) ($_POST['amount'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($name) || $amount <= 0) {
            flash('error', 'Valid name and amount are required.');
        } else {
            if ($action === 'create') {
                $stmt = $pdo->prepare('INSERT INTO payment_items (name, amount, is_active) VALUES (?, ?, ?)');
                $stmt->execute([$name, $amount, $isActive]);
                
                $feeAmount = money($amount);
                notify_role('student', 'New Fee Added', "A new fee ({$name} - {$feeAmount}) has been added.");
                
                flash('success', 'Payment item created successfully.');
            } else {
                $id = (int) $_POST['id'];
                $stmt = $pdo->prepare('UPDATE payment_items SET name = ?, amount = ?, is_active = ? WHERE id = ?');
                $stmt->execute([$name, $amount, $isActive, $id]);
                flash('success', 'Payment item updated successfully.');
            }
        }
    } elseif ($action === 'delete') {
        $id = (int) $_POST['id'];
        // Ensure no payments exist for this item before deleting (or just let foreign key SET NULL handle it)
        $pdo->prepare('DELETE FROM payment_items WHERE id = ?')->execute([$id]);
        flash('success', 'Payment item deleted successfully.');
    }
    redirect('admin/payment_items.php');
}

$paymentItems = $pdo->query('SELECT * FROM payment_items ORDER BY id DESC')->fetchAll();

$pageTitle = 'Payment Items';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Admin</p>
                <h1>Payment Items</h1>
            </div>
        </div>

        <div class="form-grid" style="grid-template-columns: 1fr 2fr; gap: 32px;">
            <section class="panel">
                <h3 style="margin-top:0;">Add New Item</h3>
                <form method="post" style="display: grid; gap: 16px;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="create">
                    
                    <label>Item Name
                        <input type="text" name="name" required placeholder="e.g., Departmental Dues">
                    </label>
                    <label>Amount (₦)
                        <input type="number" step="0.01" name="amount" required placeholder="5000.00">
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; flex-direction: row;">
                        <input type="checkbox" name="is_active" checked>
                        <span>Active (Available for payment)</span>
                    </label>
                    <button type="submit" class="cta-button" style="margin-top: 8px;">Create Item</button>
                </form>
            </section>

            <section class="panel table-panel">
                <?php if (empty($paymentItems)): ?>
                    <p style="padding: 24px; color: var(--muted); text-align: center;">No payment items found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paymentItems as $item): ?>
                                <tr>
                                    <td><strong><?= e($item['name']) ?></strong></td>
                                    <td>₦<?= number_format($item['amount'], 2) ?></td>
                                    <td><span class="status"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <button type="button" class="button button-light" style="padding: 6px 12px; font-size: 13px;" onclick="editItem(<?= htmlspecialchars(json_encode($item)) ?>)">Edit</button>
                                            <form method="post" onsubmit="return confirm('Delete this payment item?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="button button-light" style="padding: 6px 12px; font-size: 13px; color: var(--danger); border-color: var(--danger);">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </div>
    </section>
</div>

<dialog id="editDialog" class="panel" style="padding: 32px; border: 1px solid var(--line); border-radius: var(--radius); width: 400px; max-width: 90vw; max-height: 90vh; top: 50%; left: 50%; transform: translate(-50%, -50%); margin: 0; background: var(--bg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">Edit Payment Item</h3>
        <button type="button" onclick="document.getElementById('editDialog').close()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: var(--muted);">&times;</button>
    </div>
    <form method="post" style="display: grid; gap: 16px;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="edit_id">
        
        <label>Item Name
            <input type="text" name="name" id="edit_name" required>
        </label>
        <label>Amount (₦)
            <input type="number" step="0.01" name="amount" id="edit_amount" required>
        </label>
        <label style="display: flex; align-items: center; gap: 8px; flex-direction: row;">
            <input type="checkbox" name="is_active" id="edit_is_active">
            <span>Active (Available for payment)</span>
        </label>
        <button type="submit" class="cta-button" style="margin-top: 16px;">Save Changes</button>
    </form>
</dialog>

<script>
function editItem(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_name').value = item.name;
    document.getElementById('edit_amount').value = item.amount;
    document.getElementById('edit_is_active').checked = item.is_active == 1;
    document.getElementById('editDialog').showModal();
}
</script>
<?php include __DIR__ . '/../components/footer.php'; ?>
