<?php
require_once __DIR__ . '/../config/app.php';

$token = $_GET['token'] ?? '';
if (!$token) {
    flash('error', 'Invalid or missing reset token.');
    redirect('auth/login.php');
}

$stmt = $pdo->prepare('SELECT email, created_at FROM password_resets WHERE token = ?');
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    flash('error', 'Invalid or expired reset token.');
    redirect('auth/login.php');
}

// Check expiration (e.g., 1 hour)
if (strtotime($reset['created_at']) < strtotime('-1 hour')) {
    $pdo->prepare('DELETE FROM password_resets WHERE token = ?')->execute([$token]);
    flash('error', 'Reset token has expired.');
    redirect('auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirmation'] ?? '';
    
    if (strlen($password) < 8) {
        flash('error', 'Password must be at least 8 characters.');
    } elseif ($password !== $confirm) {
        flash('error', 'Passwords do not match.');
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password = ? WHERE email = ?')->execute([$hash, $reset['email']]);
        
        $pdo->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$reset['email']]);
        
        // Log the password change in the audit table (user_id lookup needed, but email is secure here)
        $userStmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $userStmt->execute([$reset['email']]);
        $u = $userStmt->fetch();
        if ($u) {
            audit_log((int) $u['id'], 'users', (int) $u['id'], null, ['action' => 'password_reset']);
        }
        
        flash('success', 'Password successfully reset! You can now log in.');
        redirect('auth/login.php');
    }
}

$pageTitle = 'Create New Password';
include __DIR__ . '/../components/header.php';
?>
<section class="auth-shell">
    <form class="auth-card" method="post">
        <?= csrf_field() ?>
        <p class="eyebrow">Account recovery</p>
        <h1>New password</h1>
        <label>New Password <input type="password" name="password" required></label>
        <label>Confirm Password <input type="password" name="password_confirmation" required></label>
        <button class="button button-dark" type="submit">Update Password</button>
    </form>
</section>
<?php include __DIR__ . '/../components/footer.php'; ?>
