<?php require_once __DIR__ . '/../config/db.php'; ensure_admin();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
if(!validate_csrf_token($_POST['csrf_token'] ?? null)){http_response_code(419);exit('Invalid CSRF');}
$subject=trim($_POST['subject'] ?? 'Cyber Sathi Notice');
$message=trim($_POST['message'] ?? '');
$type=trim($_POST['recipient_type'] ?? 'single');
$count=0;
// PHPMailer integration point (SMTP)
if(class_exists('PHPMailer\\PHPMailer\\PHPMailer')){
  // In production, wire SMTP and send here.
}
if($type==='users'){ $count=(int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(); }
elseif($type==='volunteers'){ $count=(int)$pdo->query('SELECT COUNT(*) FROM volunteers WHERE approval_status="approved"')->fetchColumn(); }
elseif($type==='participants'){ $count=(int)$pdo->query('SELECT COUNT(DISTINCT participant_name) FROM quiz_results')->fetchColumn(); }
else { $count=1; }
$pdo->prepare('INSERT INTO email_logs(subject,recipient_type,recipients_count,status) VALUES(?,?,?,?)')->execute([$subject,$type,$count,'queued']);
header('Location: /admin/index.php?mail=queued');
