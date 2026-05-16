<?php require_once __DIR__ . '/../config/db.php'; ensure_admin();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
if(!validate_csrf_token($_POST['csrf_token'] ?? null)){http_response_code(419);exit('Invalid CSRF');}
$subject=trim($_POST['subject'] ?? 'Cyber Sathi Notice');
$message=trim($_POST['message'] ?? '');
$type=trim($_POST['recipient_type'] ?? 'single');
$smtp=get_email_settings($pdo);

$recipients=[];
if($type==='users'){ $recipients=$pdo->query('SELECT email,name FROM users WHERE is_verified=1 LIMIT 200')->fetchAll(); }
elseif($type==='volunteers'){ $recipients=$pdo->query('SELECT email,name FROM volunteers WHERE approval_status="approved" LIMIT 200')->fetchAll(); }
elseif($type==='participants'){ $recipients=$pdo->query('SELECT u.email,u.name FROM quiz_results q JOIN users u ON u.id=q.user_id GROUP BY u.email,u.name LIMIT 200')->fetchAll(); }
else { $recipients=[['email'=>PRIMARY_EMAIL,'name'=>APP_NAME]]; }

$sent=0;
foreach($recipients as $r){
  if(!empty($r['email']) && send_mail_smart($smtp,$r['email'],$r['name'] ?? 'User',$subject,nl2br(e($message)))) $sent++;
}
$status = $sent>0 ? 'sent' : 'queued';
$pdo->prepare('INSERT INTO email_logs(subject,recipient_type,recipients_count,status) VALUES(?,?,?,?)')->execute([$subject,$type,count($recipients),$status]);
header('Location: /admin/index.php?mail='.$status);
