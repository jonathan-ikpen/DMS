<?php
declare(strict_types=1);

function flash(string $type, string $message): void
{
    $_SESSION['flash'][$type][] = $message;
}

function flash_messages(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}
