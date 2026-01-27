<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

require_once __DIR__ . '/../includes/auth.php';

$eventId = (int)($_GET['event_id'] ?? 0);
if ($eventId <= 0) {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

$stmt = db()->prepare('SELECT id, file_path, file_name FROM attendance_photos WHERE event_id = ? LIMIT 1');
$stmt->execute([$eventId]);
$row = $stmt->fetch();

if ($row) {
    $relative = (string)$row['file_path'];
    $prefix = rtrim(UPLOAD_DIR_REL, '/');
    if (str_starts_with($relative, $prefix)) {
        $rest = ltrim(substr($relative, strlen($prefix)), '/');
        $abs = rtrim(__DIR__ . '/../uploads/events', '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rest);
        if (is_file($abs)) {
            @unlink($abs);
        }
    }

    $del = db()->prepare('DELETE FROM attendance_photos WHERE event_id = ?');
    $del->execute([$eventId]);

    log_activity($_SESSION['admin_id'], 'DELETE', 'attendance_photos', (int)$row['id'], 'Deleted attendance photo for event ID: ' . $eventId . ', filename: ' . $row['file_name']);
}

header('Location: ' . BASE_URL . '/admin/photos.php?event_id=' . $eventId);
exit;
