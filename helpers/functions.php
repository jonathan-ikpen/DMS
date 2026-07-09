<?php
declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function base_path(string $path = ''): string
{
    return dirname(__DIR__) . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
}

function url(string $path = ''): string
{
    if (!empty($_SERVER['HTTP_HOST'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseDir = preg_replace('#/(admin|auth|staff|student)$#', '', $scriptDir) ?: '';
        $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim($baseDir, '/');

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function active_link(string $needle): string
{
    $current = $_SERVER['REQUEST_URI'] ?? '';
    return str_contains($current, $needle) ? 'is-active' : '';
}

function money(float|int|string|null $amount): string
{
    return 'NGN ' . number_format((float) $amount, 2);
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    $letters = array_map(fn ($part) => strtoupper(substr($part, 0, 1)), array_slice($parts ?: ['D'], 0, 2));
    return implode('', $letters);
}

function audit_log(int $userId, string $tableName, ?int $recordId, ?array $oldValues, ?array $newValues): void
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, table_name, record_id, old_values, new_values) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $userId,
        $tableName,
        $recordId,
        $oldValues ? json_encode($oldValues) : null,
        $newValues ? json_encode($newValues) : null
    ]);
}

function notify_user(?int $userId, string $title, string $body, string $type = 'info'): void
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, title, body, type) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $title, $body, $type]);
}

function get_setting(string $key, ?string $default = null): ?string
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $result = $stmt->fetchColumn();
    return $result !== false ? (string) $result : $default;
}

function set_setting(string $key, ?string $value, int $isSecret = 0): void
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value, is_secret) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), is_secret = VALUES(is_secret)');
    $stmt->execute([$key, $value, $isSecret]);
}

function notify_role(string $roleSlug, string $title, string $body, string $type = 'info'): void
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT users.id FROM users INNER JOIN roles ON roles.id = users.role_id WHERE roles.slug = ?');
    $stmt->execute([$roleSlug]);
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($users)) return;
    
    $insertStmt = $pdo->prepare('INSERT INTO notifications (user_id, title, body, type) VALUES (?, ?, ?, ?)');
    foreach ($users as $id) {
        $insertStmt->execute([$id, $title, $body, $type]);
    }
}
