<?php
session_start();
define('APP_NAME', 'Cyber Sathi');
define('FOUNDER_NAME', 'Deelip Jaiswal');
define('FOUNDER_LOCATION', 'Shajapur, Madhya Pradesh, India');
define('PRIMARY_EMAIL', 'cybersathideelip@gmail.com');
define('SECONDARY_EMAIL', 'jaiswaldilip8@gmail.com');
define('CONTACT_NUMBER', '+91 8889473954');
define('OFFICIAL_DOMAIN', 'https://cybersathi.free-education.fun/');

define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '');
define('RECAPTCHA_SECRET_KEY', getenv('RECAPTCHA_SECRET_KEY') ?: '');
define('FIREBASE_API_KEY', getenv('FIREBASE_API_KEY') ?: '');
define('FIREBASE_AUTH_DOMAIN', getenv('FIREBASE_AUTH_DOMAIN') ?: '');
define('FIREBASE_PROJECT_ID', getenv('FIREBASE_PROJECT_ID') ?: '');

define('SUPER_ADMIN_EMAIL', getenv('SUPER_ADMIN_EMAIL') ?: 'superadmin@cybersathi.org');
define('SUPER_ADMIN_DEFAULT_PASSWORD', getenv('SUPER_ADMIN_DEFAULT_PASSWORD') ?: 'super123');

function is_admin_logged_in(): bool { return isset($_SESSION['admin_id']); }
function is_super_admin_logged_in(): bool { return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'; }
function is_user_logged_in(): bool { return isset($_SESSION['user_id']); }
function is_volunteer_logged_in(): bool { return isset($_SESSION['volunteer_id']); }
function ensure_admin(): void { if (!is_admin_logged_in()) { header('Location: /admin/login.php'); exit; } }
function ensure_user(): void { if (!is_user_logged_in()) { header('Location: /login.php?next=quiz&msg=' . urlencode('Please register or login to participate in Cyber Awareness Quiz.')); exit; } }
function ensure_volunteer(): void { if (!is_volunteer_logged_in()) { header('Location: /volunteer-login.php'); exit; } }
function e($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_input(): string { return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">'; }
function validate_csrf_token(?string $token): bool { return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token); }

function generate_captcha(): array {
    $a=random_int(1,9); $b=random_int(1,9);
    $_SESSION['captcha_answer']=$a+$b;
    return [$a,$b];
}
function validate_captcha(?string $answer): bool {
    return isset($_SESSION['captcha_answer']) && (int)$answer === (int)$_SESSION['captcha_answer'];
}

function verify_recaptcha(?string $responseToken): bool {
    if (RECAPTCHA_SECRET_KEY === '') return true;
    if (!$responseToken) return false;
    $payload = http_build_query(['secret'=>RECAPTCHA_SECRET_KEY,'response'=>$responseToken,'remoteip'=>$_SERVER['REMOTE_ADDR'] ?? '']);
    $opts=['http'=>['method'=>'POST','header'=>"Content-type: application/x-www-form-urlencoded\r\n",'content'=>$payload,'timeout'=>10]];
    $result=@file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create($opts));
    $json=$result?json_decode($result,true):[];
    return !empty($json['success']);
}

function send_mail_smart(array $smtp, string $to, string $toName, string $subject, string $bodyHtml): bool {
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = ($smtp['smtp_host'] ?? ($smtp['host'] ?? '')) ?: 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $smtp['smtp_username'] ?? ($smtp['username'] ?? '');
            $mail->Password = $smtp['smtp_password'] ?? ($smtp['password'] ?? '');
            $mail->Port = (int)(($smtp['smtp_port'] ?? ($smtp['port'] ?? 587)));
            $enc = strtolower((string)(($smtp['encryption'] ?? 'tls')));
            $mail->SMTPSecure = $enc === 'ssl' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->setFrom($smtp['sender_email'] ?: PRIMARY_EMAIL, $smtp['sender_name'] ?: APP_NAME);
            $mail->addAddress($to, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $bodyHtml;
            $mail->send();
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
    $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: ".PRIMARY_EMAIL."\r\n";
    return @mail($to, $subject, $bodyHtml, $headers);
}
