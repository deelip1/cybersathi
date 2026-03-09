<?php require_once __DIR__ . '/config/db.php';
$email=trim($_GET['email'] ?? $_POST['email'] ?? ''); $msg=''; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)) $err='Invalid CSRF token';
  else {
    $otp=trim($_POST['otp'] ?? '');
    $stmt=$pdo->prepare('SELECT * FROM otp_verification WHERE email=? AND otp_code=? AND is_used=0 ORDER BY id DESC LIMIT 1');
    $stmt->execute([$email,$otp]); $row=$stmt->fetch();
    if($row){
      $valid = strtotime((string)$row['expires_at']) >= time();
      if($valid){
        $pdo->prepare('UPDATE otp_verification SET is_used=1 WHERE id=?')->execute([(int)$row['id']]);
        $pdo->prepare('UPDATE users SET is_verified=1 WHERE email=?')->execute([$email]);
        $msg='Email verified successfully. Please login.';
      } else $err='OTP expired';
    } else $err='Invalid OTP';
  }
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5"><h2>Email OTP Verification</h2>
<?php if($msg): ?><div class="alert alert-success"><?= e($msg) ?> <a href="/login.php">Login</a></div><?php endif; ?>
<?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post" class="row g-3"><?= csrf_input() ?><input type="hidden" name="email" value="<?= e($email) ?>">
<div class="col-md-6"><input class="form-control" name="otp" required placeholder="Enter 6-digit OTP"></div>
<div class="col-12"><button class="btn btn-primary">Verify OTP</button></div>
</form></div><?php include __DIR__ . '/includes/footer.php'; ?>
