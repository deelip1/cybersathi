<?php require_once __DIR__ . '/config/db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)) $error='Invalid CSRF token.';
  elseif(!verify_recaptcha($_POST['g-recaptcha-response'] ?? null)) $error='reCAPTCHA verification failed.';
  else {
    try {
      $email=trim($_POST['email']);
      $name=trim($_POST['name']);
      $stmt=$pdo->prepare('INSERT INTO users(name,email,mobile,password_hash,city,is_verified) VALUES(?,?,?,?,?,0)');
      $stmt->execute([$name,$email,trim($_POST['mobile']),password_hash($_POST['password'],PASSWORD_BCRYPT),trim($_POST['city'])]);
      $otp=(string)random_int(100000,999999);
      $expiresAt=date('Y-m-d H:i:s', time()+600);
      $pdo->prepare('INSERT INTO otp_verification(email,otp_code,expires_at,is_used) VALUES(?,?,?,0)')->execute([$email,$otp,$expiresAt]);
      $smtp=get_email_settings($pdo);
      $body="Dear ".e($name).",<br>Your OTP for Cyber Sathi verification is <strong>$otp</strong>. It expires in 10 minutes.";
      send_mail_smart($smtp,$email,$name,'Cyber Sathi OTP Verification',$body);
      header('Location: /verify-otp.php?email='.urlencode($email)); exit;
    } catch(Throwable $e){ $error='Registration failed. Email may already exist.'; }
  }
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5"><h2>User Registration</h2>
<div class="alert alert-info">After registration, verify email OTP, then login to play quiz.</div>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="row g-3"><?= csrf_input() ?>
<div class="col-md-6"><input class="form-control" name="name" required placeholder="Full Name"></div>
<div class="col-md-6"><input class="form-control" name="email" type="email" required placeholder="Email"></div>
<div class="col-md-6"><input class="form-control" name="mobile" required placeholder="Mobile"></div>
<div class="col-md-6"><input class="form-control" name="city" required placeholder="City"></div>
<div class="col-md-6"><input class="form-control" name="password" type="password" required placeholder="Password"></div>
<?php if (RECAPTCHA_SITE_KEY !== ''): ?><div class="col-12"><div class="g-recaptcha" data-sitekey="<?= e(RECAPTCHA_SITE_KEY) ?>"></div></div><?php endif; ?>
<div class="col-12"><button class="btn btn-primary">Register</button></div>
</form></div><?php include __DIR__ . '/includes/footer.php'; ?>
