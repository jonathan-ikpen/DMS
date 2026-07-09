<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/middleware/auth.php';

$user = require_auth(['student', 'admin']);
$reference = $_GET['ref'] ?? '';

if (empty($reference)) {
    die('Invalid reference.');
}

// Fetch payment, receipt, item, and user details
$query = "
    SELECT p.*, r.receipt_no, r.issued_at, pi.name as item_name, u.name as user_name, u.email, s.matric_no
    FROM payments p
    JOIN receipts r ON p.id = r.payment_id
    LEFT JOIN payment_items pi ON p.payment_item_id = pi.id
    JOIN users u ON p.user_id = u.id
    LEFT JOIN students s ON u.id = s.user_id
    WHERE p.reference = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$reference]);
$data = $stmt->fetch();

if (!$data) {
    die('Receipt not found.');
}

// Security: Only allow the owner or an admin to view the receipt
if ($user['role'] !== 'admin' && $user['id'] !== $data['user_id']) {
    die('Unauthorized access.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?= e($data['receipt_no']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background: #e5e7eb; color: #111827; padding: 40px 20px; }
        .receipt { max-width: 600px; margin: 0 auto; background: #fff; padding: 48px; border-radius: 8px; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f3f4f6; padding-bottom: 24px; margin-bottom: 32px; }
        .logo { font-family: 'Anton', sans-serif; font-size: 28px; color: #f97316; margin: 0; line-height: 1; letter-spacing: 0.05em; }
        .title { font-size: 24px; font-weight: 700; color: #111827; margin: 0; }
        .row { display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 14px; }
        .label { color: #6b7280; font-weight: 500; }
        .val { font-weight: 600; text-align: right; }
        .total-row { display: flex; justify-content: space-between; align-items: center; margin-top: 32px; padding-top: 24px; border-top: 2px solid #f3f4f6; }
        .total-label { font-size: 16px; font-weight: 600; color: #374151; }
        .total-val { font-size: 32px; font-weight: 700; color: #16a34a; font-family: 'Anton', sans-serif; letter-spacing: 0.02em; }
        .footer { margin-top: 48px; text-align: center; color: #9ca3af; font-size: 13px; }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div>
                <p class="logo">DMS</p>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #6b7280;">Department Management System</p>
            </div>
            <div style="text-align: right;">
                <h1 class="title">OFFICIAL RECEIPT</h1>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #6b7280;">Date: <?= date('M d, Y', strtotime($data['issued_at'])) ?></p>
            </div>
        </div>
        
        <div class="row">
            <span class="label">Receipt Number</span>
            <span class="val" style="font-family: monospace;"><?= e($data['receipt_no']) ?></span>
        </div>
        <div class="row">
            <span class="label">Transaction Reference</span>
            <span class="val" style="font-family: monospace;"><?= e($data['reference']) ?></span>
        </div>
        <div class="row">
            <span class="label">Gateway</span>
            <span class="val"><?= e($data['gateway']) ?></span>
        </div>
        
        <div style="height: 24px;"></div>
        
        <div class="row">
            <span class="label">Student Name</span>
            <span class="val"><?= e($data['user_name']) ?></span>
        </div>
        <div class="row">
            <span class="label">Matric Number</span>
            <span class="val"><?= e($data['matric_no'] ?? 'N/A') ?></span>
        </div>
        <div class="row">
            <span class="label">Payment For</span>
            <span class="val"><?= e($data['item_name'] ?? 'General Fee') ?></span>
        </div>
        
        <div class="total-row">
            <span class="total-label">Amount Paid</span>
            <span class="total-val">₦<?= number_format($data['amount'], 2) ?></span>
        </div>
        
        <div class="footer">
            <p>This is an electronically generated receipt and does not require a signature.</p>
            <button class="no-print" onclick="window.print()" style="margin-top: 16px; padding: 8px 16px; background: #111827; color: white; border: none; border-radius: 6px; cursor: pointer; font-family: inherit; font-weight: 500;">Print Receipt</button>
        </div>
    </div>
</body>
</html>
