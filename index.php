<?php require_once __DIR__ . '/config/db.php'; include __DIR__ . '/includes/header.php';
$stats=['helped'=>(int)$pdo->query('SELECT COUNT(*) FROM complaints')->fetchColumn(),'volunteers'=>(int)$pdo->query('SELECT COUNT(*) FROM volunteers WHERE approval_status="approved"')->fetchColumn(),'programs'=>(int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn(),'quiz'=>(int)$pdo->query('SELECT COUNT(*) FROM quiz_results')->fetchColumn(),'certs'=>(int)$pdo->query('SELECT COUNT(*) FROM quiz_results')->fetchColumn(),'alerts'=>(int)$pdo->query('SELECT COUNT(*) FROM fraud_alerts WHERE is_active=1')->fetchColumn()];
$alerts=$pdo->query('SELECT title,message,severity FROM fraud_alerts WHERE is_active=1 ORDER BY id DESC LIMIT 5')->fetchAll();
?>
<?php if($alerts): ?><div class="alert-banner"><strong>Live Fraud Alert:</strong> <?= e($alerts[0]['message']) ?></div><?php endif; ?>
<div class="ticker-wrap"><div class="ticker"><?php foreach($alerts as $a): ?><span>⚠ <?= e($a['title']) ?>: <?= e($a['message']) ?> &nbsp;&nbsp;&nbsp;</span><?php endforeach; ?></div></div>
<section class="hero text-light py-5"><div class="container"><h1 class="display-5 fw-bold">Cyber Sathi</h1><p class="lead">Protect Yourself From Cyber Fraud</p><a href="/complaint.php" class="btn btn-info me-2">Register Cyber Complaint</a><a href="/quiz.php" class="btn btn-outline-light me-2">Play Cyber Awareness Quiz</a><a href="/volunteer.php" class="btn btn-success me-2">Join Cyber Pioneer Volunteer</a><a href="/live-chat.php" class="btn btn-warning">Live Chat Support</a></div></section>
<section class="container py-5"><div class="row g-3 text-center">
<div class="col-md-2"><div class="card stat"><h3><?= $stats['helped'] ?></h3><p>People Helped</p></div></div>
<div class="col-md-2"><div class="card stat"><h3><?= $stats['volunteers'] ?></h3><p>Active Volunteers</p></div></div>
<div class="col-md-2"><div class="card stat"><h3><?= $stats['programs'] ?></h3><p>Programs</p></div></div>
<div class="col-md-2"><div class="card stat"><h3><?= $stats['quiz'] ?></h3><p>Quiz Participants</p></div></div>
<div class="col-md-2"><div class="card stat"><h3><?= $stats['certs'] ?></h3><p>Certificates</p></div></div>
<div class="col-md-2"><div class="card stat"><h3><?= $stats['alerts'] ?></h3><p>Fraud Alerts</p></div></div>
</div></section>
<section class="container pb-5"><a class="btn btn-primary" href="/leaderboard.php">View Leaderboard</a> <a class="btn btn-dark" href="/tools.php">Cyber Security Tools</a> <a class="btn btn-secondary" href="/courses.php">Cyber Courses</a></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
