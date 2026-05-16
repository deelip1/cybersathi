<?php require_once __DIR__ . '/../config/config.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <title><?= APP_NAME ?> - Cyber Awareness & Fraud Help</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/css/style.css" rel="stylesheet">
  <?php if (RECAPTCHA_SITE_KEY !== ''): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <?php endif; ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/index.php">Cyber Sathi</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="/complaint.php">Complaint</a></li>
        <li class="nav-item"><a class="nav-link" href="/volunteer.php">Volunteer</a></li>
        <li class="nav-item"><a class="nav-link" href="/quiz.php">Quiz</a></li>
        <li class="nav-item"><a class="nav-link" href="/ai-guidance.php">AI Guidance</a></li>
        <li class="nav-item"><a class="nav-link" href="/live-chat.php">Live Chat</a></li>
        <li class="nav-item"><a class="nav-link" href="/leaderboard.php">Leaderboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/tools.php">Scam Tools</a></li>
        <li class="nav-item"><a class="nav-link" href="/courses.php">Courses</a></li>
        <li class="nav-item"><a class="nav-link" href="/media.php">Media</a></li>
        <li class="nav-item"><a class="nav-link" href="/contact.php">Contact</a></li>
        <?php if (is_user_logged_in()): ?><li class="nav-item"><a class="nav-link" href="/user-dashboard.php">User Dashboard</a></li><?php endif; ?>
        <?php if (is_volunteer_logged_in()): ?><li class="nav-item"><a class="nav-link" href="/volunteer-dashboard.php">Volunteer Dashboard</a></li><?php endif; ?>
        <?php if (!is_user_logged_in()): ?><li class="nav-item"><a class="nav-link" href="/login.php">User Login</a></li><?php endif; ?>
        <?php if (!is_volunteer_logged_in()): ?><li class="nav-item"><a class="nav-link" href="/volunteer-login.php">Volunteer Login</a></li><?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
