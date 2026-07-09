<?php
require_once __DIR__ . '/../config/app.php';
session_unset();
session_destroy();
session_start();
flash('success', 'You have been signed out.');
redirect('auth/login.php');
