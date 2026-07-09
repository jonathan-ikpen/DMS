<?php
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../services/UserService.php';
require_guest();

$service = new UserService($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $role = clean_string($_POST['role'] ?? 'student');
    $name = clean_string($_POST['name'] ?? '');
    $email = clean_string($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (!in_array($role, ['student', 'staff'], true)) {
        flash('error', 'Only students and staff can self-register.');
        redirect('auth/register.php');
    }

    if (strlen($password) < 8) {
        flash('error', 'Password must be at least 8 characters.');
        redirect('auth/register.php');
    }

    if ($service->findByEmail($email)) {
        flash('error', 'An account already exists with that email.');
        redirect('auth/register.php');
    }

    $service->register($role, $name, $email, $password, $_POST);
    flash('success', 'Registration submitted. An administrator must activate your account.');
    redirect('auth/login.php');
}

$pageTitle = 'Register';
include __DIR__ . '/../components/header.php';
?>
<section class="auth-shell">
    <form class="auth-card wide" method="post">
        <?= csrf_field() ?>
        <p class="eyebrow">Self registration</p>
        <h1>Create account</h1>
        <label>Role
            <select name="role" data-role-select>
                <option value="student">Student</option>
                <option value="staff">Staff</option>
            </select>
        </label>
        <div class="form-grid">
            <label>Full name <input name="name" required></label>
            <label>Email <input type="email" name="email" required></label>
            <label>Password <input type="password" name="password" required minlength="8"></label>
            <label>Phone <input name="phone"></label>
            <label data-student-field>Matric number <input name="matric_no"></label>
            <label data-student-field>Level
                <select name="level"><option>ND1</option><option>ND2</option><option>HND1</option><option>HND2</option></select>
            </label>
            <label data-staff-field hidden>Staff number <input name="staff_no"></label>
            <label data-staff-field hidden>Designation <input name="designation"></label>
            <label class="span-2">Address <textarea name="address" rows="3"></textarea></label>
        </div>
        <button class="button button-primary" type="submit">Submit registration</button>
    </form>
</section>
<?php include __DIR__ . '/../components/footer.php'; ?>
