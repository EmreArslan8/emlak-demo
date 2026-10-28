<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/app');
define('DB_PATH', getenv('EMLAK_DB') ?: ROOT . '/storage/emlak.sqlite');
define('UPLOAD_DIR', ROOT . '/public/uploads');

// Uygulama alt dizinde çalışıyorsa (ör. /emlak) linkler buna göre üretilir.
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
define('BASE', $base === '/' ? '' : $base);

date_default_timezone_set('Europe/Istanbul');
mb_internal_encoding('UTF-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
}

require APP . '/helpers.php';
require APP . '/db.php';
