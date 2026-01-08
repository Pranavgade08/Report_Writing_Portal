<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Include auth functions for logging
require_once __DIR__ . '/../includes/auth.php';

$eventId = (int)($_POST['event_id'] ?? 0);
if ($eventId <= 0) {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

$evtStmt = db()->prepare('SELECT id FROM events WHERE id = ?');
$evtStmt->execute([$eventId]);
if (!$evtStmt->fetch()) {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

if (!isset($_FILES['photos'])) {
    header('Location: ' . BASE_URL . '/admin/photos.php?event_id=' . $eventId);
    exit;
}

$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

$targetDirAbs = UPLOAD_DIR_ABS . '/' . $eventId;
$targetDirRel = UPLOAD_DIR_REL . '/' . $eventId;

if (!is_dir(UPLOAD_DIR_ABS)) {
    mkdir(UPLOAD_DIR_ABS, 0775, true);
}
if (!is_dir($targetDirAbs)) {
    mkdir($targetDirAbs, 0775, true);
}

$files = $_FILES['photos'];
$count = is_array($files['name']) ? count($files['name']) : 0;

 $captions = $_POST['captions'] ?? [];
 if (!is_array($captions)) {
     $captions = [];
 }

for ($i = 0; $i < $count; $i++) {
    if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        continue;
    }

    $tmp = $files['tmp_name'][$i] ?? '';
    $mime = mime_content_type($tmp);

    if (!isset($allowed[$mime])) {
        continue;
    }

    $ext = $allowed[$mime];
    $originalName = (string)($files['name'][$i] ?? 'photo');

    $safeBase = preg_replace('/[^a-zA-Z0-9_-]+/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    $fileName = $safeBase . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    $absPath = $targetDirAbs . '/' . $fileName;
    $relPath = $targetDirRel . '/' . $fileName;

    if (!move_uploaded_file($tmp, $absPath)) {
        continue;
    }

    $caption = null;
    if (isset($captions[$i])) {
        $c = trim((string)$captions[$i]);
        if ($c !== '') {
            $caption = mb_substr($c, 0, 255);
        }
    }

    $stmt = db()->prepare('INSERT INTO event_photos (event_id, file_name, file_path, caption) VALUES (?, ?, ?, ?)');
    $stmt->execute([$eventId, $fileName, $relPath, $caption]);
    
    // Log the activity
    $photoId = (int)db()->lastInsertId();
    log_activity($_SESSION['admin_id'], 'INSERT', 'event_photos', $photoId, 'Uploaded photo for event ID: ' . $eventId . ', filename: ' . $fileName);
}

header('Location: ' . BASE_URL . '/admin/photos.php?event_id=' . $eventId);
exit;
