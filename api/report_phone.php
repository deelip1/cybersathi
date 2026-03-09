<?php require_once __DIR__ . '/../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
if(!validate_csrf_token($_POST['csrf_token'] ?? null)){http_response_code(419);exit('Invalid CSRF');}
$phone=trim($_POST['phone'] ?? ''); $type=trim($_POST['fraud_type'] ?? 'Unknown');
$stmt=$pdo->prepare('SELECT id,report_count FROM fraud_phone_reports WHERE phone=?');$stmt->execute([$phone]);$x=$stmt->fetch();
if($x){$pdo->prepare('UPDATE fraud_phone_reports SET report_count=report_count+1,fraud_type=? WHERE id=?')->execute([$type,(int)$x['id']]);}
else{$pdo->prepare('INSERT INTO fraud_phone_reports(phone,fraud_type,report_count,is_verified) VALUES(?,?,1,0)')->execute([$phone,$type]);}
header('Location: /tools.php');
