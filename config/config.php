<?php
// Configuración local opcional. Este archivo de secretos se excluye de Git y del ZIP.
$localFile = __DIR__.'/local.php';
if (is_file($localFile)) {
    $local = require $localFile;
    foreach (['NEXO_DSN','NEXO_DB_USER','NEXO_DB_PASS','NEXO_BASE_URL'] as $key) {
        if (is_array($local) && isset($local[$key]) && is_string($local[$key]) && getenv($key) === false) {
            putenv($key.'='.$local[$key]);
        }
    }
}
define('APP_NAME', 'Nexo');
define('BASE_URL', rtrim(getenv('NEXO_BASE_URL') ?: '/'.rawurlencode(basename(dirname(__DIR__))), '/'));
define('DB_DSN', getenv('NEXO_DSN') ?: 'mysql:host=127.0.0.1;port=3307;dbname=nexo_business;charset=utf8mb4');
define('DB_USER', getenv('NEXO_DB_USER') ?: 'root');
define('DB_PASS', getenv('NEXO_DB_PASS') ?: '');
