<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../services/UserService.php';
require_guest();

$service = new UserService($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = clean_string($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $user = $service->authenticate($email, $password);

    if (!$user) {
        flash('error', 'Invalid email or password.');
        redirect('auth/login.php');
    }

    if (!in_array($user['status'], ['active'], true)) {
        flash('error', 'Your account is not active. Contact the department administrator.');
        redirect('auth/login.php');
    }

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
    redirect($user['role'] . '/dashboard.php');
}

$pageTitle = 'Login';
include __DIR__ . '/../components/header.php';
?>
<section class="auth-shell">
    <form class="auth-card" method="post">
        <?= csrf_field() ?>
        <p class="eyebrow">Welcome back</p>
        <h1>Sign in</h1>
        <label>Email <input type="email" name="email" required autocomplete="email"></label>
        <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
        <button class="button button-primary" type="submit">Login</button>
        <p><a href="<?= url('auth/forgot-password.php') ?>">Forgot password?</a></p>
    </form>
</section>
<?php include __DIR__ . '/../components/footer.php'; ?>
