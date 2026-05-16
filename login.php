<?php
require_once __DIR__ . '/config/db.php';

if (is_user_logged_in()) {
    header('Location: /quiz.php?welcome=' . urlencode('Welcome to Cyber Sathi. Start the Cyber Awareness Quiz.'));
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token';
    } elseif (!validate_captcha($_POST['captcha'] ?? null)) {
        $error = 'Captcha verification failed';
    } else {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $error = 'Please enter valid login details.';
        } else {
            $stmt = $pdo->prepare('SELECT id, password_hash, name, is_verified FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && (int)($user['is_verified'] ?? 0) === 1 && password_verify($password, (string)$user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_name'] = (string)$user['name'];
                header('Location: /quiz.php?welcome=' . urlencode('Welcome to Cyber Sathi. Start the Cyber Awareness Quiz.'));
                exit;
            }

            $error = 'Invalid credentials or email not verified.';
        }
    }
}

[$a, $b] = generate_captcha();
include __DIR__ . '/includes/header.php';
?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-7">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
          <div class="text-center mb-4">
            <img src="/assets/logo.svg" width="64" alt="Cyber Sathi Logo">
            <h2 class="mt-2 mb-0">Cyber Sathi Login</h2>
          </div>

          <?php if (isset($_GET['msg']) && $_GET['msg'] !== ''): ?><div class="alert alert-warning"><?= e((string)$_GET['msg']) ?></div><?php endif; ?>
          <?php if (isset($_GET['welcome']) && $_GET['welcome'] !== ''): ?><div class="alert alert-success"><?= e((string)$_GET['welcome']) ?></div><?php endif; ?>
          <?php if ($error !== ''): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

          <form method="post" class="row g-3" novalidate>
            <?= csrf_input() ?>
            <div class="col-12 col-md-6">
              <label class="form-label" for="loginEmail">Email</label>
              <input id="loginEmail" class="form-control" name="email" type="email" value="<?= e($email) ?>" autocomplete="email" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="loginPassword">Password</label>
              <input id="loginPassword" class="form-control" name="password" type="password" autocomplete="current-password" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label" for="captchaInput">Captcha: <?= (int)$a ?> + <?= (int)$b ?> = ?</label>
              <input id="captchaInput" class="form-control" name="captcha" inputmode="numeric" required>
            </div>
            <div class="col-12 d-flex gap-2 flex-wrap">
              <button class="btn btn-primary" type="submit">Login</button>
              <a class="btn btn-outline-secondary" href="/register.php">Sign Up</a>
              <a class="btn btn-link" href="/forgot-password.php">Forgot Password?</a>
            </div>
          </form>

          <?php if (GOOGLE_CLIENT_ID !== ''): ?>
          <div class="mt-4">
            <form id="google-login-form" action="/google-login.php" method="post">
              <?= csrf_input() ?>
              <input type="hidden" name="credential" id="googleCredential">
            </form>
            <div id="g_id_onload" data-client_id="<?= e(GOOGLE_CLIENT_ID) ?>" data-callback="onGoogleSignIn"></div>
            <div class="g_id_signin" data-type="standard" data-theme="outline" data-size="large" data-text="signin_with" data-shape="pill"></div>
          </div>
          <script src="https://accounts.google.com/gsi/client" async defer></script>
          <script>
            function onGoogleSignIn(response) {
              if (!response || !response.credential) return;
              document.getElementById('googleCredential').value = response.credential;
              document.getElementById('google-login-form').submit();
            }
          </script>
          <?php else: ?>
            <button class="btn btn-outline-danger mt-4" disabled>Login with Google (Configure GOOGLE_CLIENT_ID)</button>
          <?php endif; ?>

          <p class="mt-3 mb-0">Don't have an account? <a href="/register.php">Register Now</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
