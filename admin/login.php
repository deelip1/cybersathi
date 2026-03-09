<?php require_once __DIR__ . '/../config/db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){
    $error='Invalid CSRF token';
  } else {
    $stmt=$pdo->prepare('SELECT id,password_hash,name FROM admin WHERE email=?');
    $stmt->execute([trim($_POST['email'])]);
    $a=$stmt->fetch();
    if($a && password_verify($_POST['password'],$a['password_hash'])){ $_SESSION['admin_id']=$a['id']; $_SESSION['admin_name']=$a['name']; header('Location: /admin/index.php'); exit; }
    $error='Invalid credentials';
  }
}
include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-5"><h2>Admin Login</h2>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="row g-3">
<?= csrf_input() ?>
<div class="col-md-6"><input class="form-control" name="email" type="email" required></div><div class="col-md-6"><input class="form-control" name="password" type="password" required></div><div class="col-12"><button class="btn btn-primary">Login</button></div></form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
