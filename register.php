<?php require_once __DIR__ . '/config/db.php';
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $stmt=$pdo->prepare('INSERT INTO users(name,email,mobile,password_hash,city) VALUES(?,?,?,?,?)');
  $stmt->execute([trim($_POST['name']),trim($_POST['email']),trim($_POST['mobile']),password_hash($_POST['password'], PASSWORD_BCRYPT),trim($_POST['city'])]);
  $message='Registration successful. Please login.';
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5"><h2>User Registration</h2>
<?php if($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
<form method="post" class="row g-3">
  <div class="col-md-6"><input class="form-control" name="name" required placeholder="Name"></div>
  <div class="col-md-6"><input class="form-control" name="email" type="email" required placeholder="Email"></div>
  <div class="col-md-6"><input class="form-control" name="mobile" required placeholder="Mobile"></div>
  <div class="col-md-6"><input class="form-control" name="city" required placeholder="City"></div>
  <div class="col-md-6"><input class="form-control" name="password" type="password" required placeholder="Password"></div>
  <div class="col-12"><button class="btn btn-primary">Register</button></div>
</form></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
