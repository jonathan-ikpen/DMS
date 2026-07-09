<?php
require_once __DIR__ . '/../config/app.php';
header('Content-Type: application/json');

$user = current_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mark as read
    $input = json_decode(file_get_contents('php://input'), true);
    if (!empty($input['ids']) && is_array($input['ids'])) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO notification_reads (notification_id, user_id) VALUES (?, ?)');
        foreach ($input['ids'] as $nid) {
            $stmt->execute([(int)$nid, $userId]);
        }
    }
    echo json_encode(['success' => true]);
    exit;
}

// Fetch unread
$stmt = $pdo->prepare('
    SELECT n.* FROM notifications n
    LEFT JOIN notification_reads nr ON n.id = nr.notification_id AND nr.user_id = ?
    WHERE (n.user_id = ? OR n.user_id IS NULL)
    AND nr.notification_id IS NULL
    ORDER BY n.created_at DESC
    LIMIT 20
');
$stmt->execute([$userId, $userId]);
$unread = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['notifications' => $unread]);
