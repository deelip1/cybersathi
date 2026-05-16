<?php require_once __DIR__ . '/config/db.php';
$msg=''; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)) $err='Invalid CSRF token';
  else {
    $pdo->prepare('INSERT INTO feedback(name,email,message) VALUES(?,?,?)')->execute([
      trim($_POST['name']), trim($_POST['email']), trim($_POST['message'])
    ]);
    $msg='Thank you for your feedback.';
  }
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5">
  <h2>Feedback</h2>
  <p>Share your suggestions to improve Cyber Sathi services.</p>
  <?php if($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
  <?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <form method="post" class="row g-3"><?= csrf_input() ?>
    <div class="col-md-6"><input name="name" class="form-control" required placeholder="Name"></div>
    <div class="col-md-6"><input name="email" type="email" class="form-control" required placeholder="Email"></div>
    <div class="col-12"><textarea name="message" class="form-control" rows="4" required placeholder="Your message"></textarea></div>
    <div class="col-12"><button class="btn btn-primary">Submit Feedback</button></div>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
