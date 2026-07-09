<?php
$dir = __DIR__ . '/admin';
$files = glob($dir . '/*.php');
foreach ($files as $file) {
    if (basename($file) === 'payment_items.php') continue;
    
    $content = file_get_contents($file);
    if (strpos($content, "'Payment Items'") === false) {
        $content = str_replace(
            "'Payments' => 'admin/payments.php'",
            "'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php'",
            $content
        );
        file_put_contents($file, $content);
    }
}
echo "Nav updated.";
