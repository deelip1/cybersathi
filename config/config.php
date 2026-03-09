<?php
session_start();
define('APP_NAME', 'Cyber Sathi');
define('FOUNDER_NAME', 'Deelip Jaiswal');
define('FOUNDER_LOCATION', 'Shajapur, Madhya Pradesh, India');
define('PRIMARY_EMAIL', 'cybersathideelip@gmail.com');
define('SECONDARY_EMAIL', 'jaiswaldilip8@gmail.com');
define('CONTACT_NUMBER', '+91 8889473954');
define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '');
define('RECAPTCHA_SECRET_KEY', getenv('RECAPTCHA_SECRET_KEY') ?: '');
define('FIREBASE_API_KEY', getenv('FIREBASE_API_KEY') ?: '');
define('FIREBASE_AUTH_DOMAIN', getenv('FIREBASE_AUTH_DOMAIN') ?: '');
define('FIREBASE_PROJECT_ID', getenv('FIREBASE_PROJECT_ID') ?: '');

function is_admin_logged_in(): bool { return isset($_SESSION['admin_id']); }
function is_user_logged_in(): bool { return isset($_SESSION['user_id']); }
function is_volunteer_logged_in(): bool { return isset($_SESSION['volunteer_id']); }
function ensure_admin(): void { if (!is_admin_logged_in()) { header('Location: /admin/login.php'); exit; } }
function ensure_user(): void { if (!is_user_logged_in()) { header('Location: /login.php'); exit; } }
function ensure_volunteer(): void { if (!is_volunteer_logged_in()) { header('Location: /volunteer-login.php'); exit; } }
function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function validate_csrf_token(?string $token): bool {
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function verify_recaptcha(?string $responseToken): bool {
    if (RECAPTCHA_SECRET_KEY === '') {
        return true; // local/dev mode
    }
    if (!$responseToken) {
        return false;
    }
    $payload = http_build_query([
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $responseToken,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => $payload,
            'timeout' => 10,
        ],
    ];
    $context = stream_context_create($opts);
    $result = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    if ($result === false) return false;
    $json = json_decode($result, true);
    return !empty($json['success']);
}
