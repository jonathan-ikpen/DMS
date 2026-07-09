<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/middleware/auth.php';

$user = require_auth(['student']);
$reference = $_GET['reference'] ?? '';

if (empty($reference)) {
    flash('error', 'No payment reference provided.');
    redirect('student/payments.php');
}

$stmt = $pdo->prepare('SELECT * FROM payments WHERE reference = ? AND user_id = ?');
$stmt->execute([$reference, $user['id']]);
$payment = $stmt->fetch();

if (!$payment) {
    flash('error', 'Payment record not found.');
    redirect('student/payments.php');
}

if ($payment['status'] === 'paid') {
    flash('success', 'Payment was already successful.');
    redirect('student/payments.php');
}

// Perform Server-to-Server verification with Paystack
$secretKey = get_setting('paystack_secret_key', 'sk_test_47120c3fdeea5f2a038541a4a22e9d21e1fce671');
if (!$secretKey) {
    die("Payment verification gateway is not configured.");
}
$url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secretKey",
    "Cache-Control: no-cache"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$verified = false;

if ($response && $httpCode === 200) {
    $result = json_decode($response, true);
    
    // Paystack returns standard data.status for transactions
    if (isset($result['status']) && $result['status'] === true) {
        if (isset($result['data']['status']) && $result['data']['status'] === 'success') {
            
            // Double check that the amount hasn't been spoofed
            // Paystack amount is in kobo, database is in Naira
            $paystackAmountInNaira = $result['data']['amount'] / 100;
            
            if ($paystackAmountInNaira == $payment['amount']) {
                $verified = true;
            } else {
                error_log("Payment amount mismatch. Expected {$payment['amount']}, got {$paystackAmountInNaira}");
            }
        }
    }
} else {
    error_log("Paystack API call failed. HTTP Code: $httpCode. Response: $response");
}

if ($verified) {
    try {
        $pdo->beginTransaction();
        
        $pdo->prepare('UPDATE payments SET status = "paid", paid_at = CURRENT_TIMESTAMP WHERE id = ?')
            ->execute([$payment['id']]);
            
        $receiptNo = 'RCPT-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $pdo->prepare('INSERT INTO receipts (payment_id, receipt_no) VALUES (?, ?)')
            ->execute([$payment['id'], $receiptNo]);
            
        $pdo->commit();
        
        $paymentAmount = money($payment['amount']);
        notify_user($user['id'], 'Payment Successful', "Your payment of {$paymentAmount} was successful. Receipt No: {$receiptNo}");
        notify_role('admin', 'Payment Received', "A payment of {$paymentAmount} was just received from {$user['name']}.");
        
        flash('success', 'Payment successful! Your receipt has been generated.');
    } catch (Exception $e) {
        $pdo->rollBack();
        flash('error', 'Error processing successful payment.');
    }
} else {
    $pdo->prepare('UPDATE payments SET status = "failed" WHERE id = ?')->execute([$payment['id']]);
    flash('error', 'Payment verification failed or transaction was not successful.');
}

redirect('student/payments.php');
