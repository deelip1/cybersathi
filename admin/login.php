<?php require_once __DIR__ . '/../config/db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){
    $error='Invalid CSRF token';
  } else {
    $stmt=$pdo->prepare('SELECT id,password_hash,name,role FROM admin WHERE email=?');
    $stmt->execute([trim($_POST['email'])]);
    $a=$stmt->fetch();
    if($a && password_verify($_POST['password'],$a['password_hash'])){
      $_SESSION['admin_id']=$a['id'];
      $_SESSION['admin_name']=$a['name'];
      $_SESSION['admin_role']=$a['role'] ?? 'admin';
      header('Location: /admin/index.php'); exit;
    }
    $error='Invalid credentials';
  }
}
include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-5">
  <div class="wp-shell mx-auto" style="max-width:900px">
    <aside class="wp-sidebar p-4">
      <h4>Cyber Sathi</h4>
      <p class="text-white-50 mb-0">Super Admin / Admin Panel</p>
      <hr class="text-white-50">
      <small class="d-block text-white-50">Default Super Admin</small>
      <small><?= e(SUPER_ADMIN_EMAIL) ?></small>
    </aside>
    <section class="wp-main p-4">
      <h2 class="mb-3">WordPress-style Admin Login</h2>
      <?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <?= csrf_input() ?>
        <div class="col-12"><label class="form-label">Email</label><input class="form-control" name="email" type="email" required></div>
        <div class="col-12"><label class="form-label">Password</label><input class="form-control" name="password" type="password" required></div>
        <div class="col-12"><button class="btn btn-primary">Login Dashboard</button></div>
      </form>
    </section>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
