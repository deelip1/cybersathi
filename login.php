<?php require_once __DIR__ . '/config/db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){ $error='Invalid CSRF token'; }
  else {
    $stmt=$pdo->prepare('SELECT id,password_hash,name FROM users WHERE email=?');
    $stmt->execute([trim($_POST['email'])]);
    $u=$stmt->fetch();
    if($u && password_verify($_POST['password'],$u['password_hash'])){
      $_SESSION['user_id']=$u['id']; $_SESSION['user_name']=$u['name'];
      header('Location: /user-dashboard.php'); exit;
    }
    $error='Invalid credentials';
  }
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5"><h2>User Login</h2>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="row g-3">
  <?= csrf_input() ?>
  <div class="col-md-6"><input class="form-control" name="email" type="email" required placeholder="Email"></div>
  <div class="col-md-6"><input class="form-control" name="password" type="password" required placeholder="Password"></div>
  <div class="col-12"><button class="btn btn-primary">Login</button></div>
</form></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
