<?php require_once __DIR__ . '/config/db.php';
$msg='';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)) $err='Invalid CSRF token';
  else {
    $email=trim($_POST['email']);
    $token=bin2hex(random_bytes(24));
    $expires = ($dbDriver ?? 'mysql')==='mysql' ? date('Y-m-d H:i:s', time()+1800) : date('Y-m-d H:i:s', time()+1800);
    $pdo->prepare('INSERT INTO password_reset_tokens(email,token,expires_at,is_used) VALUES(?,?,?,0)')->execute([$email,$token,$expires]);
    $link=OFFICIAL_DOMAIN.'reset-password.php?token='.$token;
    $smtp=get_email_settings($pdo);
    send_mail_smart($smtp,$email,$email,'Cyber Sathi Password Reset',"Click to reset password (valid 30 min): <a href='$link'>$link</a>");
    $msg='If account exists, password reset link has been sent.';
  }
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5"><h2>Forgot Password</h2>
<?php if($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?><?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post" class="row g-3"><?= csrf_input() ?><div class="col-md-6"><input name="email" class="form-control" type="email" required placeholder="Registered Email"></div><div class="col-12"><button class="btn btn-primary">Send Reset Link</button></div></form>
</div><?php include __DIR__ . '/includes/footer.php'; ?>
