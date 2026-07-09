<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $public_key = $_POST['paystack_public_key'] ?? '';
    $secret_key = $_POST['paystack_secret_key'] ?? '';
    
    set_setting('paystack_public_key', $public_key);
    set_setting('paystack_secret_key', $secret_key, 1);
    
    flash('success', 'Paystack API keys have been saved successfully.');
    redirect('admin/paystack_integration.php');
}

$pageTitle = 'Paystack Integration';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];

$publicKey = get_setting('paystack_public_key', 'pk_test_60fdc6c1602b775169f7937afc04af51c1219c8f');
$secretKey = get_setting('paystack_secret_key', 'sk_test_47120c3fdeea5f2a038541a4a22e9d21e1fce671');

include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Settings</p>
                <h1>Paystack Integration</h1>
            </div>
            <a href="<?= url('admin/settings.php') ?>" style="color: var(--muted); font-size: 14px;">Back to Settings</a>
        </div>
        
        <section class="panel" style="max-width: 600px;">
            <form method="post" class="form-grid" style="display: flex; flex-direction: column; gap: 20px;">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <label>Paystack Public Key
                    <div style="position: relative;">
                        <input type="password" name="paystack_public_key" value="<?= e($publicKey) ?>" placeholder="pk_test_..." required style="padding-right: 48px;">
                        <button type="button" onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.querySelector('.eye-open').style.display = input.type === 'password' ? 'block' : 'none'; this.querySelector('.eye-closed').style.display = input.type === 'password' ? 'none' : 'block';" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--muted); padding: 4px; display: flex;">
                            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </label>
                
                <label>Paystack Secret Key
                    <div style="position: relative;">
                        <input type="password" name="paystack_secret_key" value="<?= e($secretKey) ?>" placeholder="sk_test_..." required style="padding-right: 48px;">
                        <button type="button" onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.querySelector('.eye-open').style.display = input.type === 'password' ? 'block' : 'none'; this.querySelector('.eye-closed').style.display = input.type === 'password' ? 'none' : 'block';" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--muted); padding: 4px; display: flex;">
                            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </label>
                
                <button type="submit" class="cta-button">Save Integration Settings</button>
            </form>
        </section>
    </section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
