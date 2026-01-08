<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$title = $title ?? APP_NAME;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo h($title); ?></title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css" />
</head>
<body>
  <header class="nav <?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/') !== false) ? 'admin-nav' : ''; ?>">
    <div class="container nav-inner">
      <div class="brand">
        DEOGIRI COLLEGE
        <small>Event Report Portal</small>
      </div>
      <nav class="nav-links">
        <a class="btn" href="<?php echo BASE_URL; ?>/index.php">Home</a>
        <a class="btn" href="<?php echo BASE_URL; ?>/events.php">Events</a>
        <a class="btn" href="<?php echo BASE_URL; ?>/about.php">About</a>
        <a class="btn" href="<?php echo BASE_URL; ?>/contact.php">Contact</a>
        <?php if (is_admin_logged_in()): ?>
          <a class="btn primary" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Admin Dashboard</a>
          <a class="btn" href="<?php echo BASE_URL; ?>/admin/logout.php">Logout</a>
        <?php else: ?>
          <a class="btn primary" href="<?php echo BASE_URL; ?>/admin/login.php">Admin Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <main class="container">
