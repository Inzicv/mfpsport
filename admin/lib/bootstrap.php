<?php

declare(strict_types=1);

require_once __DIR__ . '/GitHubRepository.php';
require_once __DIR__ . '/Content.php';
require_once __DIR__ . '/ImageProcessor.php';

header('X-Robots-Tag: noindex, nofollow, noarchive', true);
header('X-Content-Type-Options: nosniff', true);
header('X-Frame-Options: DENY', true);
header('Referrer-Policy: no-referrer', true);
header('Permissions-Policy: camera=(), microphone=(), geolocation=()', true);
header("Content-Security-Policy: default-src 'self'; base-uri 'none'; form-action 'self'; frame-ancestors 'none'; img-src 'self' data:; style-src 'self'; script-src 'self'", true);
header('Cache-Control: no-store, private, max-age=0', true);

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_name('mfp_admin');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

$configPath = getenv('MFP_ADMIN_CONFIG');
if (!is_string($configPath) || $configPath === '') {
    $configPath = dirname(__DIR__, 2) . '/private/mfpsport-admin.php';
}
if (!is_file($configPath)) {
    http_response_code(503);
    exit('<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="robots" content="noindex"><title>Administration indisponible</title><body><h1>Administration en cours de configuration</h1><p>Revenez dans quelques instants.</p></body></html>');
}
$config = require $configPath;
if (!is_array($config)) {
    throw new RuntimeException('Configuration invalide.');
}

$sessionLifetime = max(300, min(7200, (int) ($config['session_lifetime'] ?? 1800)));
if (isset($_SESSION['last_activity']) && time() - (int) $_SESSION['last_activity'] > $sessionLifetime) {
    $_SESSION = [];
    session_regenerate_id(true);
}
$_SESSION['last_activity'] = time();

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        throw new RuntimeException('Cette page a expiré. Rechargez-la puis recommencez.');
    }
}

function is_authenticated(): bool
{
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

function require_authentication(): void
{
    if (!is_authenticated()) {
        redirect('?action=login');
    }
}

function redirect(string $location): never
{
    header('Location: ' . $location, true, 303);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** @return array{type:string,message:string}|null */
function pull_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function admin_storage_path(array $config): string
{
    $path = (string) ($config['storage_path'] ?? '');
    if ($path === '') {
        $path = sys_get_temp_dir() . '/mfpsport-admin-state';
    }
    if (!is_dir($path) && !mkdir($path, 0700, true) && !is_dir($path)) {
        throw new RuntimeException('Le stockage sécurisé de l’administration est indisponible.');
    }
    return rtrim($path, DIRECTORY_SEPARATOR);
}

/** @return array{attempts:int,first:int,locked_until:int} */
function login_state(array $config): array
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $file = admin_storage_path($config) . DIRECTORY_SEPARATOR . 'login-' . hash('sha256', $ip) . '.json';
    if (!is_file($file)) {
        return ['attempts' => 0, 'first' => time(), 'locked_until' => 0];
    }
    $state = json_decode((string) @file_get_contents($file), true);
    if (!is_array($state) || time() - (int) ($state['first'] ?? 0) > 3600) {
        return ['attempts' => 0, 'first' => time(), 'locked_until' => 0];
    }
    return [
        'attempts' => (int) ($state['attempts'] ?? 0),
        'first' => (int) ($state['first'] ?? time()),
        'locked_until' => (int) ($state['locked_until'] ?? 0),
    ];
}

function record_login_failure(array $config): void
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $file = admin_storage_path($config) . DIRECTORY_SEPARATOR . 'login-' . hash('sha256', $ip) . '.json';
    $state = login_state($config);
    $state['attempts']++;
    if ($state['attempts'] >= 5) {
        $state['locked_until'] = time() + min(900, 30 * (2 ** min(5, $state['attempts'] - 5)));
    }
    @file_put_contents($file, json_encode($state, JSON_THROW_ON_ERROR), LOCK_EX);
}

function clear_login_failures(array $config): void
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $file = admin_storage_path($config) . DIRECTORY_SEPARATOR . 'login-' . hash('sha256', $ip) . '.json';
    if (is_file($file)) {
        @unlink($file);
    }
}

function repository(array $config): GitHubRepository
{
    static $repository = null;
    if (!$repository instanceof GitHubRepository) {
        $repository = new GitHubRepository((array) ($config['github'] ?? []));
    }
    return $repository;
}

function post_string(string $name, int $max = 5000): string
{
    $value = $_POST[$name] ?? '';
    return is_string($value) ? trim(mb_substr($value, 0, $max)) : '';
}

function valid_web_url(string $value): bool
{
    if ($value === '') {
        return true;
    }
    if (filter_var($value, FILTER_VALIDATE_URL) === false) {
        return false;
    }
    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true);
}

function load_markdown_entries(GitHubRepository $repository, string $directory): array
{
    $entries = [];
    foreach ($repository->listDirectory($directory) as $item) {
        $name = (string) ($item['name'] ?? '');
        if (!str_ends_with($name, '.md')) {
            continue;
        }
        $path = $directory . '/' . $name;
        $file = $repository->getFile($path);
        $parsed = Content::parseMarkdown($file['content']);
        $entries[] = [
            'id' => substr($name, 0, -3),
            'path' => $path,
            'sha' => $file['sha'],
            'data' => $parsed['data'],
            'body' => $parsed['body'],
        ];
    }
    return $entries;
}

function assert_safe_id(string $id): void
{
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $id)) {
        throw new RuntimeException('Identifiant de contenu invalide.');
    }
}
