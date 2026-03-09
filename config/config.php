<?php
session_start();
define('APP_NAME', 'Cyber Sathi');
define('FOUNDER_NAME', 'Deelip Jaiswal');
define('FOUNDER_LOCATION', 'Shajapur, Madhya Pradesh, India');
define('PRIMARY_EMAIL', 'cybersathideelip@gmail.com');
define('SECONDARY_EMAIL', 'jaiswaldilip8@gmail.com');
define('CONTACT_NUMBER', '+91 8889473954');
function is_admin_logged_in(): bool { return isset($_SESSION['admin_id']); }
function ensure_admin(): void { if (!is_admin_logged_in()) { header('Location: /admin/login.php'); exit; } }
function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
