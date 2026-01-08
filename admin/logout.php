<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

admin_logout();
header('Location: ' . BASE_URL . '/index.php');
exit;
