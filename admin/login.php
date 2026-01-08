<?php
require_once __DIR__ . '/../includes/header.php';

ensure_session_started();

$error = '';

if (is_admin_logged_in()) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } elseif (!admin_login($username, $password)) {
        $error = 'Invalid login details.';
    } else {
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit;
    }
}

$title = 'Admin Login - ' . APP_NAME;
?>

<h2 class="section-title">Faculty / Admin Login</h2>

<div class="admin-login">
  <div class="card">
    <div class="admin-login-form">
      <?php if ($error): ?>
        <div class="alert error" style="margin-bottom:12px"><?php echo h($error); ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="field">
          <label>Username</label>
          <input class="input" name="username" placeholder="admin" autocomplete="username" />
        </div>
        <div class="field" style="margin-top:10px">
          <label>Password</label>
          <input class="input" type="password" name="password" placeholder="Password" autocomplete="current-password" />
        </div>
        <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap">
          <button class="btn primary" type="submit">Login</button>
          <a class="btn" href="<?php echo BASE_URL; ?>/index.php">Back</a>
        </div>
      </form>

      <div class="muted" style="margin-top:12px">
        Default demo login: <span class="kbd">admin</span> / <span class="kbd">admin123</span>
      </div>
    </div>
    
    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
      <h3 style="margin-top:0">What admin can do</h3>
      <div class="muted" style="line-height:1.6">
        Add new events, edit or delete existing events, and upload photos event-wise.
        Students can view events and download the event report PDF.
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
