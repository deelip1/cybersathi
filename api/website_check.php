<?php require_once __DIR__ . '/../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
if(!validate_csrf_token($_POST['csrf_token'] ?? null)){http_response_code(419);exit('Invalid CSRF');}
$url=trim($_POST['url'] ?? '');
$danger=false; $susp=false;
if(stripos($url,'https://')!==0) $susp=true;
if(preg_match('/(free-money|guaranteed-return|task-earn|otp-verify)/i',$url)) $danger=true;
$status=$danger?'Dangerous':($susp?'Suspicious':'Safe');
header('Location: /tools.php?website_status='.urlencode($status).'&website_url='.urlencode($url));
