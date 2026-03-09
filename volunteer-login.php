<?php require_once __DIR__ . '/config/db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){
    $error='Invalid CSRF token';
  } else {
    $stmt=$pdo->prepare('SELECT id,name,password_hash,approval_status FROM volunteers WHERE email=?');
    $stmt->execute([trim($_POST['email'])]);
    $v=$stmt->fetch();
    if($v && $v['approval_status']==='approved' && password_verify($_POST['password'],$v['password_hash'])){
      $_SESSION['volunteer_id']=$v['id']; $_SESSION['volunteer_name']=$v['name'];
      header('Location: /volunteer-dashboard.php'); exit;
    }
    $error='Invalid credentials or volunteer not approved yet.';
  }
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5"><h2>Volunteer Login</h2>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="row g-3">
  <?= csrf_input() ?>
  <div class="col-md-6"><input class="form-control" name="email" type="email" required placeholder="Volunteer Email"></div>
  <div class="col-md-6"><input class="form-control" name="password" type="password" required placeholder="Password"></div>
  <div class="col-12"><button class="btn btn-primary">Login</button></div>
</form></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
