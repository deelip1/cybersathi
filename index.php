<?php require_once __DIR__ . '/config/db.php'; include __DIR__ . '/includes/header.php';
$stats = [
 'helped' => (int)$pdo->query('SELECT COUNT(*) FROM complaints')->fetchColumn(),
 'volunteers' => (int)$pdo->query('SELECT COUNT(*) FROM volunteers WHERE approval_status="approved"')->fetchColumn(),
 'programs' => (int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn(),
 'quiz' => (int)$pdo->query('SELECT COUNT(*) FROM quiz_results')->fetchColumn(),
];
?>
<section class="hero text-light py-5">
  <div class="container">
    <h1 class="display-5 fw-bold">Cyber Sathi</h1>
    <p class="lead">Protect Yourself From Cyber Fraud</p>
    <a href="/complaint.php" class="btn btn-info me-2">Register Cyber Complaint</a>
    <a href="/quiz.php" class="btn btn-outline-light me-2">Play Cyber Awareness Quiz</a>
    <a href="/volunteer.php" class="btn btn-success me-2">Join Cyber Pioneer Volunteer</a>
    <a href="/media.php" class="btn btn-warning">Watch Awareness Videos</a>
  </div>
</section>
<section class="container py-5">
  <div class="row g-3 text-center">
    <div class="col-md-3"><div class="card stat"><h3><?= $stats['helped'] ?></h3><p>People Helped</p></div></div>
    <div class="col-md-3"><div class="card stat"><h3><?= $stats['volunteers'] ?></h3><p>Cyber Volunteers</p></div></div>
    <div class="col-md-3"><div class="card stat"><h3><?= $stats['programs'] ?></h3><p>Awareness Programs</p></div></div>
    <div class="col-md-3"><div class="card stat"><h3><?= $stats['quiz'] ?></h3><p>Quiz Participants</p></div></div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
