<?php
declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

define('PROJECT_ROOT', dirname(__DIR__));

$configFile = __DIR__ . '/config.php';
if (!is_readable($configFile)) {
    http_response_code(500);
    exit('Admin: config.php fehlt. Kopiere admin/config.example.php nach admin/config.php.');
}

/** @var array{password_hash: string} $ADMIN_CONFIG */
$ADMIN_CONFIG = require $configFile;

require_once PROJECT_ROOT . '/includes/events-store.php';

function admin_csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function admin_verify_csrf(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function admin_require_login(): void
{
    if (empty($_SESSION['admin_ok'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * @param array{password_hash: string} $config
 */
function admin_password_ok(array $config, string $plain): bool
{
    return password_verify($plain, $config['password_hash']);
}
