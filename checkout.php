<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/middleware/auth.php';

$user = require_auth(['student']);
$itemId = (int) ($_GET['item_id'] ?? 0);

if ($itemId <= 0) {
    die('Invalid payment item.');
}

$stmt = $pdo->prepare('SELECT * FROM payment_items WHERE id = ? AND is_active = 1');
$stmt->execute([$itemId]);
$item = $stmt->fetch();

if (!$item) {
    die('Payment item not found or inactive.');
}

$paystackPublicKey = get_setting('paystack_public_key', 'pk_test_60fdc6c1602b775169f7937afc04af51c1219c8f');
if (!$paystackPublicKey) {
    die('Payment gateway is not fully configured. Please contact the administrator.');
}

// Generate transaction reference or fetch existing
$existing = $pdo->prepare('SELECT reference FROM payments WHERE user_id = ? AND payment_item_id = ? AND status = "pending" LIMIT 1');
$existing->execute([$user['id'], $itemId]);
$pending = $existing->fetch();

if ($pending) {
    $reference = $pending['reference'];
} else {
    // Paystack usually expects unique alphanumeric strings, standard DMS format works fine.
    $reference = 'PAY-' . strtoupper(bin2hex(random_bytes(6)));
    $pdo->prepare('INSERT INTO payments (user_id, payment_item_id, gateway, reference, amount, status) VALUES (?, ?, "Paystack", ?, ?, "pending")')
        ->execute([$user['id'], $itemId, $reference, $item['amount']]);
}

$pageTitle = 'Checkout';

// Paystack strictly validates emails and rejects `.test` TLDs even in sandbox mode.
// We'll safely convert it to `.com` just for the Paystack payload so the widget opens.
$paystackEmail = str_replace('.test', '.com', $user['email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?= e($item['name']) ?></title>
    <link rel="stylesheet" href="<?= url('assets/css/app.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://js.paystack.co/v1/inline.js"></script>
</head>
<body>
    <div style="min-height: 100vh; display: grid; place-items: center; padding: 24px;">
        <div class="auth-card" style="width: 100%; max-width: 480px; padding: 40px; text-align: center;">
            <p class="eyebrow">Secure Checkout</p>
            <h1 style="font-size: 32px; margin-bottom: 24px;">Confirm Payment</h1>
            
            <div style="background: var(--soft); border-radius: var(--radius); padding: 24px; margin-bottom: 32px; border: 1px solid var(--line);">
                <h3 style="margin: 0 0 8px 0; font-size: 18px; color: var(--muted);"><?= e($item['name']) ?></h3>
                <p style="font-size: 36px; font-weight: 700; color: var(--accent); margin: 0; font-family: 'Anton', sans-serif; letter-spacing: 0.02em;">₦<?= number_format($item['amount'], 2) ?></p>
            </div>
            
            <button type="button" class="cta-button" onclick="makePayment()" style="width: 100%; height: 56px; font-size: 16px;">Pay via Paystack</button>
            <p style="margin-top: 16px; font-size: 14px; color: var(--muted);">Secured by Paystack.</p>
            
            <a href="<?= url('student/payments.php') ?>" style="display: inline-block; margin-top: 24px; font-size: 14px; color: var(--muted); text-decoration: underline;">Cancel and return</a>
        </div>
    </div>

    <script>
        function makePayment() {
            var handler = PaystackPop.setup({
                key: '<?= e($paystackPublicKey) ?>',
                email: '<?= e($paystackEmail) ?>',
                amount: <?= $item['amount'] * 100 ?>, // Paystack requires amount in kobo
                currency: 'NGN',
                ref: '<?= e($reference) ?>',
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Student Name",
                            variable_name: "student_name",
                            value: "<?= e($user['name']) ?>"
                        }
                    ]
                },
                callback: function(response) {
                    console.log('Payment complete! Reference: ' + response.reference);
                    window.location.href = "<?= url('verify_payment.php?reference=') ?>" + response.reference;
                },
                onClose: function() {
                    console.log('Window closed.');
                }
            });
            
            handler.openIframe();
        }
    </script>
</body>
</html>
