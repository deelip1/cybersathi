<?php require_once __DIR__ . '/../config/config.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= APP_NAME ?> - Cyber Awareness & Fraud Help</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/css/style.css" rel="stylesheet">
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
        <li class="nav-item"><a class="nav-link" href="/media.php">Media</a></li>
        <li class="nav-item"><a class="nav-link" href="/contact.php">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>
