<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'department_management_system';
const DB_USER = 'root';
const DB_PASS = '';
const APP_NAME = 'Department Management System';
const APP_URL = 'http://localhost/DMS';
const SESSION_TIMEOUT = 1800;

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Database connection failed. Check config/connect.php and import database/schema.sql.');
}
