<?php require_once __DIR__ . '/config/db.php';
$message=''; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){ $error='Invalid CSRF token.'; }
  elseif(!verify_recaptcha($_POST['g-recaptcha-response'] ?? null)){ $error='reCAPTCHA verification failed.'; }
  else {
    try {
      $stmt=$pdo->prepare('INSERT INTO users(name,email,mobile,password_hash,city) VALUES(?,?,?,?,?)');
      $stmt->execute([trim($_POST['name']),trim($_POST['email']),trim($_POST['mobile']),password_hash($_POST['password'], PASSWORD_BCRYPT),trim($_POST['city'])]);
      $message='Registration successful. Please login.';
    } catch(Throwable $e){ $error='Registration failed. Email may already exist.'; }
  }
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5"><h2>User Registration</h2>
<?php if($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="row g-3">
  <?= csrf_input() ?>
  <div class="col-md-6"><input class="form-control" name="name" required placeholder="Name"></div>
  <div class="col-md-6"><input class="form-control" name="email" type="email" required placeholder="Email"></div>
  <div class="col-md-6"><input class="form-control" name="mobile" required placeholder="Mobile"></div>
  <div class="col-md-6"><input class="form-control" name="city" required placeholder="City"></div>
  <div class="col-md-6"><input class="form-control" name="password" type="password" required placeholder="Password"></div>
  <?php if (RECAPTCHA_SITE_KEY !== ""): ?><div class="col-12"><div class="g-recaptcha" data-sitekey="<?= e(RECAPTCHA_SITE_KEY) ?>"></div></div><?php endif; ?>
  <div class="col-12"><button class="btn btn-primary">Register</button></div>
</form></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
