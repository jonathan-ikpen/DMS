<?php
require_once __DIR__ . '/../config/app.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    
    // Check if user exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND deleted_at IS NULL');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $token = bin2hex(random_bytes(32));
        
        // Remove old tokens
        $pdo->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$email]);
        
        $pdo->prepare('INSERT INTO password_resets (email, token) VALUES (?, ?)')
            ->execute([$email, $token]);
            
        $resetLink = url("auth/reset-password.php?token={$token}");
        
        // In a real app, send email here. Since this is local:
        error_log("Password reset link for {$email}: {$resetLink}");
        flash('success', "A reset link has been generated. For testing locally, check the URL below:<br><br><a href='{$resetLink}' style='color: white; text-decoration: underline;'>Click here to reset password</a>");
    } else {
        // Generic message to prevent email enumeration
        flash('success', 'If the email exists, a reset instruction has been sent.');
    }
    
    redirect('auth/forgot-password.php');
}
$pageTitle = 'Forgot Password';
include __DIR__ . '/../components/header.php';
?>
<section class="auth-shell">
    <form class="auth-card" method="post">
        <?= csrf_field() ?>
        <p class="eyebrow">Account recovery</p>
        <h1>Reset password</h1>
        <label>Email <input type="email" name="email" required></label>
        <button class="button button-dark" type="submit">Request reset</button>
        <div style="margin-top: 24px; text-align: center;">
            <a href="<?= url('auth/login.php') ?>" style="color: var(--muted); font-size: 14px; text-decoration: none;">Back to login</a>
        </div>
    </form>
</section>
<?php include __DIR__ . '/../components/footer.php'; ?>
