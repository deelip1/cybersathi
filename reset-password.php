<?php require_once __DIR__ . '/config/db.php';
$token=trim($_GET['token'] ?? $_POST['token'] ?? ''); $err=''; $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)) $err='Invalid CSRF token';
  else {
    $stmt=$pdo->prepare('SELECT * FROM password_reset_tokens WHERE token=? AND is_used=0 ORDER BY id DESC LIMIT 1');$stmt->execute([$token]);$row=$stmt->fetch();
    if(!$row) $err='Invalid reset token';
    else {
      $valid = strtotime($row['expires_at']) >= time();
      if(!$valid) $err='Reset link expired';
      else {
        $hash=password_hash($_POST['password'], PASSWORD_BCRYPT);
        $u=$pdo->prepare('UPDATE users SET password_hash=? WHERE email=?'); $u->execute([$hash,$row['email']]);
        if($u->rowCount()===0){ $v=$pdo->prepare('UPDATE volunteers SET password_hash=? WHERE email=?'); $v->execute([$hash,$row['email']]); }
        $pdo->prepare('UPDATE password_reset_tokens SET is_used=1 WHERE id=?')->execute([(int)$row['id']]);
        $msg='Password reset successful. Please login.';
      }
    }
  }
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5"><h2>Reset Password</h2>
<?php if($msg): ?><div class="alert alert-success"><?= e($msg) ?> <a href="/login.php">Login</a></div><?php endif; ?><?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post" class="row g-3"><?= csrf_input() ?><input type="hidden" name="token" value="<?= e($token) ?>"><div class="col-md-6"><input name="password" class="form-control" type="password" required placeholder="New Password"></div><div class="col-12"><button class="btn btn-primary">Reset Password</button></div></form>
</div><?php include __DIR__ . '/includes/footer.php'; ?>
