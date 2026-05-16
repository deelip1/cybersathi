<?php require_once __DIR__ . '/config/db.php';
if(is_user_logged_in()){ header('Location: /quiz.php?welcome='.urlencode('Welcome to Cyber Sathi. Start the Cyber Awareness Quiz.')); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)) $error='Invalid CSRF token';
  elseif(!validate_captcha($_POST['captcha'] ?? null)) $error='Captcha verification failed';
  else {
    $stmt=$pdo->prepare('SELECT id,password_hash,name,is_verified FROM users WHERE email=?');
    $stmt->execute([trim($_POST['email'])]);
    $u=$stmt->fetch();
    if($u && (int)($u['is_verified'] ?? 0)===1 && password_verify($_POST['password'],$u['password_hash'])){
      $_SESSION['user_id']=$u['id']; $_SESSION['user_name']=$u['name'];
      header('Location: /quiz.php?welcome='.urlencode('Welcome to Cyber Sathi. Start the Cyber Awareness Quiz.')); exit;
    }
    $error='Invalid credentials or email not verified.';
  }
}
[$a,$b]=generate_captcha();
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5">
  <div class="row justify-content-center"><div class="col-lg-8">
    <div class="card shadow-sm p-4">
      <div class="text-center mb-3">
        <img src="/assets/logo.svg" width="64" alt="Cyber Sathi Logo">
        <h2 class="mt-2">Cyber Sathi Login</h2>
      </div>
      <?php if(isset($_GET['msg'])): ?><div class="alert alert-warning"><?= e($_GET['msg']) ?></div><?php endif; ?>
      <?php if(isset($_GET['welcome'])): ?><div class="alert alert-success"><?= e($_GET['welcome']) ?></div><?php endif; ?>
      <?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

      <form method="post" class="row g-3 mb-2"><?= csrf_input() ?>
        <div class="col-md-6"><input class="form-control" name="email" type="email" required placeholder="Email"></div>
        <div class="col-md-6"><input class="form-control" name="password" type="password" required placeholder="Password"></div>
        <div class="col-md-6"><label class="form-label">Captcha: <?= $a ?> + <?= $b ?> = ?</label><input class="form-control" name="captcha" required></div>
        <div class="col-12 d-flex gap-2 flex-wrap">
          <button class="btn btn-primary">Login</button>
          <a class="btn btn-outline-secondary" href="/register.php">Sign Up</a>
          <a class="btn btn-link" href="/forgot-password.php">Forgot Password?</a>
        </div>
      </form>

      <?php if (GOOGLE_CLIENT_ID !== ''): ?>
      <div class="mt-3">
        <form id="google-login-form" action="/google-login.php" method="post"><?= csrf_input() ?><input type="hidden" name="credential" id="googleCredential"></form>
        <div id="g_id_onload"
             data-client_id="<?= e(GOOGLE_CLIENT_ID) ?>"
             data-callback="onGoogleSignIn"></div>
        <div class="g_id_signin" data-type="standard" data-theme="outline" data-size="large" data-text="signin_with" data-shape="pill"></div>
      </div>
      <script src="https://accounts.google.com/gsi/client" async defer></script>
      <script>
        function onGoogleSignIn(response){
          if(!response || !response.credential) return;
          document.getElementById('googleCredential').value = response.credential;
          document.getElementById('google-login-form').submit();
        }
      </script>
      <?php else: ?>
        <button class="btn btn-outline-danger mt-3" disabled>Login with Google (Configure GOOGLE_CLIENT_ID)</button>
      <?php endif; ?>

      <p class="mt-3 mb-0">Don't have an account? <a href="/register.php">Register Now</a></p>
    </div>
  </div></div>
</div>
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
