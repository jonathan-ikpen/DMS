<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/middleware/auth.php';

// Ensure the user is logged in
$user = require_auth(['student', 'staff', 'admin']);

$file = $_GET['file'] ?? '';

if (empty($file) || strpos($file, '/') !== false || strpos($file, '\\') !== false) {
    die('Invalid file request.');
}

// Verify authorization to access this file
if ($user['role'] !== 'admin') {
    // Check if the file belongs to the logged-in user
    $stmt = $pdo->prepare('SELECT id FROM document_uploads WHERE file_path = ? AND user_id = ?');
    $stmt->execute([$file, $user['id']]);
    if (!$stmt->fetch()) {
        die('Unauthorized access to this document.');
    }
}

$path = __DIR__ . '/storage/uploads/' . $file;

if (!file_exists($path)) {
    die('File not found.');
}

$mime = mime_content_type($path);
header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
header('Content-Disposition: inline; filename="' . basename($file) . '"');
header('Content-Length: ' . filesize($path));

readfile($path);
exit;
