<?php
require_once __DIR__ . '/../vendor/autoload.php';

define('DIR_MODE', 0775);
define('FILE_MODE', 0664);

define('APP_DIR', dirname(__DIR__) . '/');
define('PUBLIC_DIR', APP_DIR . '/public');

$storagePath = APP_DIR . '/../storage/';
$appMode = 'dev';

if (!empty($_ENV['APP_MODE']) && $_ENV['APP_MODE'] === 'live') {
    $appMode = 'live';
    $storagePath = APP_DIR . '/storage/';
    if (!file_exists($storagePath) && !mkdir($storagePath, DIR_MODE, true) && !is_dir($storagePath)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $storagePath));
    }
}
define('APP_MODE', $appMode);
define('STORAGE', realpath($storagePath). '/');


define('S3_BUCKET', 'uploader');


$env = Dotenv\Dotenv::create(APP_DIR);
$env->load();

define('DB_HOST', $_ENV['DB_HOST'] ?? '');
define('DB_PORT', $_ENV['DB_PORT'] ?? '');
define('DB_DATABASE', $_ENV['DB_DATABASE'] ?? '');
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? '');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('TOKEN', $_ENV['TOKEN'] ?? '');

define('DOMAIN_URL', $_SERVER['HTTP_ORIGIN'] ?? 'localhost');
define('WS_SERVER', 'wss://ws.human-connection.social:8085');

require_once APP_DIR . '/app/application.php';
