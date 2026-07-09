<?php
declare(strict_types=1);

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../helpers/flash.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('DMS_SESSION');
    session_start();
}

if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

if (time() - (int) $_SESSION['last_activity'] > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    session_start();
    flash('error', 'Your session expired. Please sign in again.');
}

$_SESSION['last_activity'] = time();
