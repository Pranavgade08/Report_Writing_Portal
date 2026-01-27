<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

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

if (!isset($_FILES['attendance'])) {
    header('Location: ' . BASE_URL . '/admin/photos.php?event_id=' . $eventId);
    exit;
}

$f = $_FILES['attendance'];
if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    header('Location: ' . BASE_URL . '/admin/photos.php?event_id=' . $eventId);
    exit;
}

$tmp = (string)($f['tmp_name'] ?? '');
$mime = @mime_content_type($tmp);
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
if (!$mime || !isset($allowed[$mime])) {
    header('Location: ' . BASE_URL . '/admin/photos.php?event_id=' . $eventId);
    exit;
}

$targetDirAbs = UPLOAD_DIR_ABS . '/' . $eventId;
$targetDirRel = UPLOAD_DIR_REL . '/' . $eventId;

if (!is_dir(UPLOAD_DIR_ABS)) {
    @mkdir(UPLOAD_DIR_ABS, 0775, true);
}
if (!is_dir($targetDirAbs)) {
    @mkdir($targetDirAbs, 0775, true);
}

$ext = $allowed[$mime];
$fileName = 'attendance_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$absPath = $targetDirAbs . '/' . $fileName;
$relPath = $targetDirRel . '/' . $fileName;

if (!move_uploaded_file($tmp, $absPath)) {
    header('Location: ' . BASE_URL . '/admin/photos.php?event_id=' . $eventId);
    exit;
}

$oldStmt = db()->prepare('SELECT id, file_path, file_name FROM attendance_photos WHERE event_id = ? LIMIT 1');
$oldStmt->execute([$eventId]);
$old = $oldStmt->fetch();

if ($old) {
    $relative = (string)$old['file_path'];
    $prefix = rtrim(UPLOAD_DIR_REL, '/');
    if (str_starts_with($relative, $prefix)) {
        $rest = ltrim(substr($relative, strlen($prefix)), '/');
        $abs = rtrim(__DIR__ . '/../uploads/events', '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rest);
        if (is_file($abs)) {
            @unlink($abs);
        }
    }
}

$stmt = db()->prepare(
    'INSERT INTO attendance_photos (event_id, file_name, file_path) VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE file_name = VALUES(file_name), file_path = VALUES(file_path), uploaded_at = CURRENT_TIMESTAMP'
);
$stmt->execute([$eventId, $fileName, $relPath]);

$recordId = $old ? (int)$old['id'] : (int)db()->lastInsertId();
log_activity($_SESSION['admin_id'], 'UPSERT', 'attendance_photos', $recordId, 'Uploaded attendance photo for event ID: ' . $eventId . ', filename: ' . $fileName);

header('Location: ' . BASE_URL . '/admin/photos.php?event_id=' . $eventId);
exit;
