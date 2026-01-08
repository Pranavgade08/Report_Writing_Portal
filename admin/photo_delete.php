<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Include auth functions for logging
require_once __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$eventId = (int)($_GET['event_id'] ?? 0);

if ($id <= 0 || $eventId <= 0) {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

$stmt = db()->prepare('SELECT id, file_path, file_name FROM event_photos WHERE id = ? AND event_id = ?');
$stmt->execute([$id, $eventId]);
$photo = $stmt->fetch();

if ($photo) {
    $relative = (string)$photo['file_path'];
    $prefix = rtrim(UPLOAD_DIR_REL, '/');

    if (str_starts_with($relative, $prefix)) {
        $rest = ltrim(substr($relative, strlen($prefix)), '/');
        $abs = rtrim(__DIR__ . '/../uploads/events', '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rest);
        if (is_file($abs)) {
            @unlink($abs);
        }
    }

    $del = db()->prepare('DELETE FROM event_photos WHERE id = ? AND event_id = ?');
    $del->execute([$id, $eventId]);
    
    // Log the activity
    log_activity($_SESSION['admin_id'], 'DELETE', 'event_photos', $id, 'Deleted photo for event ID: ' . $eventId . ', filename: ' . $photo['file_name']);
}

header('Location: ' . BASE_URL . '/admin/photos.php?event_id=' . $eventId);
exit;
