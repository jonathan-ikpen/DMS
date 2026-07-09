<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';

function require_guest(): void
{
    if (current_user()) {
        redirect(current_user()['role'] . '/dashboard.php');
    }
}

function require_auth(array $roles = []): array
{
    $user = current_user();
    if (!$user) {
        flash('error', 'Please sign in to continue.');
        redirect('auth/login.php');
    }

    if ($roles && !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }

    return $user;
}
