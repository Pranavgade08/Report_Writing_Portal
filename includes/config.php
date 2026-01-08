<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'event_portal');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_NAME', 'College Event Report Portal');
define('BASE_URL', '/eventcolleges');

define('UPLOAD_DIR_ABS', __DIR__ . '/../uploads/events');
define('UPLOAD_DIR_REL', BASE_URL . '/uploads/events');

if (!is_dir(UPLOAD_DIR_ABS)) {
    @mkdir(UPLOAD_DIR_ABS, 0775, true);
}
