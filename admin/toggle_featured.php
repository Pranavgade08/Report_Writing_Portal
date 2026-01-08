<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

$id = (int)$_GET['id'];
if ($id <= 0) {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

// Check if the 'featured' column exists
$hasFeaturedColumn = db()->query("SHOW COLUMNS FROM events LIKE 'featured'")->fetch();

if (!$hasFeaturedColumn) {
    // If the column doesn't exist, redirect with an error message
    $_SESSION['error'] = 'Featured column does not exist in events table. Please update your database schema.';
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

// Get current featured status
$stmt = db()->prepare('SELECT featured FROM events WHERE id = ?');
$stmt->execute([$id]);
$result = $stmt->fetch();

if (!$result) {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

// Toggle the featured status
$newFeaturedStatus = $result['featured'] ? 0 : 1;

$stmt = db()->prepare('UPDATE events SET featured = ? WHERE id = ?');
$stmt->execute([$newFeaturedStatus, $id]);

header('Location: ' . BASE_URL . '/admin/events.php');
exit;