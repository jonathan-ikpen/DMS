<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../middleware/auth.php';

$user = current_user();
if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$slotId = (int) ($input['slot_id'] ?? 0);
$newDay = trim($input['new_day'] ?? '');

$validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

if ($slotId <= 0 || !in_array($newDay, $validDays)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid data provided']);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE timetable SET day_of_week = ? WHERE id = ?');
    $stmt->execute([$newDay, $slotId]);
    
    // Log the change
    audit_log($user['id'], 'timetable', $slotId, null, ['action' => 'drag_update', 'new_day' => $newDay]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Timetable update failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
