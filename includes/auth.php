<?php

require_once __DIR__ . '/db.php';

function ensure_session_started(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function is_admin_logged_in(): bool
{
    ensure_session_started();
    return isset($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function admin_login(string $username, string $password): bool
{
    ensure_session_started();

    $stmt = db()->prepare('SELECT id, username, password_hash, full_name FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin) {
        return false;
    }

    if (!password_verify($password, $admin['password_hash'])) {
        return false;
    }

    $_SESSION['admin_id'] = (int)$admin['id'];
    $_SESSION['admin_name'] = $admin['full_name'];

    return true;
}

function admin_logout(): void
{
    ensure_session_started();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function log_activity(int $admin_id, string $action, string $table_name, int $record_id, string $description = ''): void
{
    $stmt = db()->prepare(
        'INSERT INTO activity_logs (admin_id, action, table_name, record_id, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $admin_id,
        $action,
        $table_name,
        $record_id,
        $description,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}

function get_activity_logs(int $limit = 50): array
{
    $stmt = db()->prepare(
        'SELECT al.*, a.full_name as admin_name FROM activity_logs al
         JOIN admins a ON a.id = al.admin_id
         ORDER BY al.created_at DESC LIMIT ?'
    );
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}
