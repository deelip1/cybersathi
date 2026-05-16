<?php require_once __DIR__ . '/../config/db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){
    $error='Invalid CSRF token';
  } elseif(!validate_captcha($_POST['captcha'] ?? null)){
    $error='Captcha verification failed';
  } else {
    try {
      $stmt=$pdo->prepare('SELECT id,password_hash,name,role,state_id,district_id FROM admin WHERE email=? LIMIT 1');
      $stmt->execute([trim((string)$_POST['email'])]);
      $a=$stmt->fetch();
      if($a && password_verify((string)$_POST['password'],(string)$a['password_hash'])){
        session_regenerate_id(true);
        $_SESSION['admin_id']=(int)$a['id'];
        $_SESSION['admin_name']=$a['name'];
        $_SESSION['admin_role']=$a['role'] ?? ROLE_ADMIN;
        $_SESSION['admin_state_id']=isset($a['state_id']) ? (int)$a['state_id'] : null;
        $_SESSION['admin_district_id']=isset($a['district_id']) ? (int)$a['district_id'] : null;
        header('Location: /admin/index.php'); exit;
      }
      $error='Invalid credentials';
    } catch (Throwable $e){
      $error='Unable to login at the moment. Please try again.';
    }
  }
}
[$a,$b]=generate_captcha();
include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-5">
  <div class="wp-shell mx-auto" style="max-width:900px">
    <aside class="wp-sidebar p-4">
      <h4>Cyber Sathi</h4>
      <p class="text-white-50 mb-0">Super Admin / State Cell / District Cell / Admin</p>
      <hr class="text-white-50">
      <small class="d-block text-white-50">Default Super Admin</small>
      <small><?= e(SUPER_ADMIN_EMAIL) ?></small>
    </aside>
    <section class="wp-main p-4">
      <h2 class="mb-3">Enterprise Admin Login</h2>
      <?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <?= csrf_input() ?>
        <div class="col-12"><label class="form-label">Email</label><input class="form-control" name="email" type="email" required></div>
        <div class="col-12"><label class="form-label">Password</label><input class="form-control" name="password" type="password" required></div>
        <div class="col-12"><label class="form-label">Captcha: <?= $a ?> + <?= $b ?> = ?</label><input class="form-control" name="captcha" required></div>
        <div class="col-12"><button class="btn btn-primary">Login Dashboard</button></div>
      </form>
    </section>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
