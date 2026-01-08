<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Include auth functions for logging
require_once __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

// Get event details before deletion for logging
$eventStmt = db()->prepare('SELECT title FROM events WHERE id = ?');
$eventStmt->execute([$id]);
$event = $eventStmt->fetch();

$stmt = db()->prepare('DELETE FROM events WHERE id = ?');
$stmt->execute([$id]);

// Log the activity if event was found
if ($event) {
    log_activity($_SESSION['admin_id'], 'DELETE', 'events', $id, 'Deleted event: ' . $event['title']);
}

$dir = UPLOAD_DIR_ABS . '/' . $id;
if (is_dir($dir)) {
    $files = glob($dir . '/*');
    if (is_array($files)) {
        foreach ($files as $f) {
            if (is_file($f)) {
                @unlink($f);
            }
        }
    }
    @rmdir($dir);
}

header('Location: ' . BASE_URL . '/admin/events.php');
exit;
