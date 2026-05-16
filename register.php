<?php
require_once __DIR__ . '/config/db.php';

$error = '';
$form = [
    'name' => '',
    'email' => '',
    'mobile' => '',
    'city' => '',
    'agree_terms' => false,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['name'] = trim((string)($_POST['name'] ?? ''));
    $form['email'] = trim((string)($_POST['email'] ?? ''));
    $form['mobile'] = trim((string)($_POST['mobile'] ?? ''));
    $form['city'] = trim((string)($_POST['city'] ?? ''));
    $form['agree_terms'] = !empty($_POST['agree_terms']);

    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } elseif (!verify_recaptcha($_POST['g-recaptcha-response'] ?? null)) {
        $error = 'reCAPTCHA verification failed.';
    } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password === '' || strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/\d/', $password) || !preg_match('/[A-Za-z]/', $password)) {
        $error = 'Password must include letters and numbers.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Password and confirm password do not match.';
    } elseif (!$form['agree_terms']) {
        $error = 'Please agree to Cyber Sathi terms.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users(name, email, mobile, password_hash, city, login_type, is_verified) VALUES(?, ?, ?, ?, ?, "normal", 0)');
            $stmt->execute([
                $form['name'],
                $form['email'],
                $form['mobile'],
                password_hash($password, PASSWORD_BCRYPT),
                $form['city'],
            ]);

            $otp = (string)random_int(100000, 999999);
            $expiresAt = date('Y-m-d H:i:s', time() + 600);
            $pdo->prepare('INSERT INTO otp_verification(email, otp_code, expires_at, is_used) VALUES(?, ?, ?, 0)')
                ->execute([$form['email'], $otp, $expiresAt]);

            $smtp = get_email_settings($pdo);
            $body = 'Dear ' . e($form['name']) . ',<br>Your OTP for Cyber Sathi verification is <strong>' . e($otp) . '</strong>. It expires in 10 minutes.';
            send_mail_smart($smtp, $form['email'], $form['name'], 'Cyber Sathi OTP Verification', $body);

            header('Location: /verify-otp.php?email=' . urlencode($form['email']));
            exit;
        } catch (Throwable $e) {
            $error = 'Registration failed. Email may already exist.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
          <h2 class="mb-3">User Registration</h2>
          <div class="alert alert-info">After registration, verify your email OTP, then log in to start the Cyber Awareness Quiz.</div>

          <?php if ($error !== ''): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

          <form method="post" class="row g-3" novalidate>
            <?= csrf_input() ?>
            <div class="col-md-6">
              <label class="form-label" for="regName">Full Name</label>
              <input id="regName" class="form-control" name="name" required value="<?= e($form['name']) ?>" placeholder="Full Name">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="regEmail">Email</label>
              <input id="regEmail" class="form-control" name="email" type="email" required value="<?= e($form['email']) ?>" placeholder="Email">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="regMobile">Mobile Number</label>
              <input id="regMobile" class="form-control" name="mobile" required value="<?= e($form['mobile']) ?>" placeholder="Mobile Number">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="regCity">City</label>
              <input id="regCity" class="form-control" name="city" required value="<?= e($form['city']) ?>" placeholder="City">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="regPassword">Password</label>
              <input id="regPassword" class="form-control" name="password" type="password" required placeholder="Password (min 8 chars)">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="regConfirmPassword">Confirm Password</label>
              <input id="regConfirmPassword" class="form-control" name="confirm_password" type="password" required placeholder="Confirm Password">
            </div>
            <div class="col-12 form-check ms-1">
              <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" value="1" <?= $form['agree_terms'] ? 'checked' : '' ?> required>
              <label class="form-check-label" for="agree_terms">I agree to Cyber Sathi terms.</label>
            </div>
            <?php if (RECAPTCHA_SITE_KEY !== ''): ?><div class="col-12"><div class="g-recaptcha" data-sitekey="<?= e(RECAPTCHA_SITE_KEY) ?>"></div></div><?php endif; ?>
            <div class="col-12 d-flex gap-2 flex-wrap">
              <button class="btn btn-primary" type="submit">Sign Up</button>
              <a class="btn btn-outline-secondary" href="/login.php">Already have an account? Login</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/config/db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)) $error='Invalid CSRF token.';
  elseif(!verify_recaptcha($_POST['g-recaptcha-response'] ?? null)) $error='reCAPTCHA verification failed.';
  elseif(trim((string)($_POST['password'] ?? '')) !== trim((string)($_POST['confirm_password'] ?? ''))) $error='Password and confirm password do not match.';
  elseif(empty($_POST['agree_terms'])) $error='Please agree to Cyber Sathi terms.';
  else {
    try {
      $email=trim($_POST['email']);
      $name=trim($_POST['name']);
      $stmt=$pdo->prepare('INSERT INTO users(name,email,mobile,password_hash,city,login_type,is_verified) VALUES(?,?,?,?,?,"normal",0)');
      $stmt->execute([$name,$email,trim($_POST['mobile']),password_hash($_POST['password'],PASSWORD_BCRYPT),trim($_POST['city'])]);
      $otp=(string)random_int(100000,999999);
      $expiresAt=date('Y-m-d H:i:s', time()+600);
      $pdo->prepare('INSERT INTO otp_verification(email,otp_code,expires_at,is_used) VALUES(?,?,?,0)')->execute([$email,$otp,$expiresAt]);
      $smtp=get_email_settings($pdo);
      $body="Dear ".e($name).",<br>Your OTP for Cyber Sathi verification is <strong>$otp</strong>. It expires in 10 minutes.";
      send_mail_smart($smtp,$email,$name,'Cyber Sathi OTP Verification',$body);
      header('Location: /verify-otp.php?email='.urlencode($email)); exit;
    } catch(Throwable $e){ $error='Registration failed. Email may already exist.'; }
$message=''; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){ $error='Invalid CSRF token.'; }
  elseif(!verify_recaptcha($_POST['g-recaptcha-response'] ?? null)){ $error='reCAPTCHA verification failed.'; }
  else {
    try {
      $stmt=$pdo->prepare('INSERT INTO users(name,email,mobile,password_hash,city) VALUES(?,?,?,?,?)');
      $stmt->execute([
        trim($_POST['name']),
        trim($_POST['email']),
        trim($_POST['mobile']),
        password_hash($_POST['password'], PASSWORD_BCRYPT),
        trim($_POST['city'])
      ]);

      $_SESSION['user_id'] = (int)$pdo->lastInsertId();
      $_SESSION['user_name'] = trim($_POST['name']);
      header('Location: /quiz.php?welcome=1');
      exit;
    } catch(Throwable $e){
      $error='Registration failed. Email may already exist.';
    }
  }
}
include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5"><h2>User Registration</h2>
<div class="alert alert-info">After registration, verify email OTP, then login to start the Cyber Awareness Quiz.</div>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="row g-3"><?= csrf_input() ?>
<div class="col-md-6"><input class="form-control" name="name" required placeholder="Full Name"></div>
<div class="col-md-6"><input class="form-control" name="email" type="email" required placeholder="Email"></div>
<div class="col-md-6"><input class="form-control" name="mobile" required placeholder="Mobile Number"></div>
<div class="col-md-6"><input class="form-control" name="city" required placeholder="City"></div>
<div class="col-md-6"><input class="form-control" name="password" type="password" required placeholder="Password"></div>
<div class="col-md-6"><input class="form-control" name="confirm_password" type="password" required placeholder="Confirm Password"></div>
<div class="col-12 form-check">
  <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" value="1" required>
  <label class="form-check-label" for="agree_terms">I agree to Cyber Sathi terms.</label>
</div>
<?php if (RECAPTCHA_SITE_KEY !== ''): ?><div class="col-12"><div class="g-recaptcha" data-sitekey="<?= e(RECAPTCHA_SITE_KEY) ?>"></div></div><?php endif; ?>
<div class="col-12"><button class="btn btn-primary">Sign Up</button></div>
<div class="col-12">Already have an account? <a href="/login.php">Login</a></div>
</form></div><?php include __DIR__ . '/includes/footer.php'; ?>
<p class="text-muted">After successful registration, you will be redirected to the Cyber Awareness Quiz automatically.</p>
<?php if($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="row g-3">
  <?= csrf_input() ?>
  <div class="col-md-6"><input class="form-control" name="name" required placeholder="Full Name"></div>
  <div class="col-md-6"><input class="form-control" name="email" type="email" required placeholder="Email"></div>
  <div class="col-md-6"><input class="form-control" name="mobile" required placeholder="Mobile"></div>
  <div class="col-md-6"><input class="form-control" name="city" required placeholder="City"></div>
  <div class="col-md-6"><input class="form-control" name="password" type="password" required placeholder="Password"></div>
  <?php if (RECAPTCHA_SITE_KEY !== ""): ?><div class="col-12"><div class="g-recaptcha" data-sitekey="<?= e(RECAPTCHA_SITE_KEY) ?>"></div></div><?php endif; ?>
  <div class="col-12"><button class="btn btn-primary">Register & Start Quiz</button></div>
</form></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
