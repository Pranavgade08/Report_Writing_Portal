<?php

require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbCandidates = array_values(array_unique([
        DB_NAME,
        'event_portal',
        'event_report',
    ]));

    $lastError = null;
    foreach ($dbCandidates as $dbName) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . $dbName . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            // Verify basic tables exist; if not, try next candidate
            $hasEvents = $pdo->query("SHOW TABLES LIKE 'events'")->fetch();
            if (!$hasEvents) {
                $pdo = null;
                continue;
            }

            break;
        } catch (PDOException $e) {
            $lastError = $e;
            $pdo = null;
        }
    }

    if (!$pdo instanceof PDO) {
        throw $lastError instanceof PDOException ? $lastError : new PDOException('Database connection failed');
    }

    try {
        $col = $pdo->query("SHOW COLUMNS FROM events LIKE 'featured'")->fetch();
        if (!$col) {
            $pdo->exec("ALTER TABLE events ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 0");
            $pdo->exec("CREATE INDEX idx_events_featured ON events (featured)");
        }
    } catch (Throwable $e) {
    }

    try {
        $col = $pdo->query("SHOW COLUMNS FROM event_photos LIKE 'caption'")->fetch();
        if (!$col) {
            $pdo->exec("ALTER TABLE event_photos ADD COLUMN caption VARCHAR(255) NULL");
        }
    } catch (Throwable $e) {
    }

    return $pdo;
}

function h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
