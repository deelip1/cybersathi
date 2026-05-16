<?php require_once __DIR__ . '/config/db.php'; include __DIR__ . '/includes/header.php';
$scalar = function(string $sql, int $default = 0) use ($pdo): int { try { return (int)$pdo->query($sql)->fetchColumn(); } catch(Throwable $e){ return $default; } };
$list = function(string $sql) use ($pdo): array { try { return $pdo->query($sql)->fetchAll(); } catch(Throwable $e){ return []; } };
$stats=[
  'helped'=>$scalar('SELECT COUNT(*) FROM complaints'),
  'volunteers'=>$scalar('SELECT COUNT(*) FROM volunteers WHERE approval_status="approved"'),
  'programs'=>$scalar('SELECT COUNT(*) FROM events'),
  'quiz'=>$scalar('SELECT COUNT(*) FROM quiz_results'),
  'certs'=>$scalar('SELECT COUNT(*) FROM quiz_results'),
  'alerts'=>$scalar('SELECT COUNT(*) FROM fraud_alerts WHERE is_active=1')
];
$alerts=$list('SELECT title,message,severity FROM fraud_alerts WHERE is_active=1 ORDER BY id DESC LIMIT 5');
$news=$list("SELECT title,event_date,thumbnail,event_type FROM events WHERE event_type IN ('News','Media','Seminar','Webinar') ORDER BY event_date DESC, id DESC LIMIT 6");
$videos=$list('SELECT title,created_at,thumbnail,video_url FROM videos ORDER BY id DESC LIMIT 3');
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
<section class="container pb-4"><h3>Official Cyber Safety Portals</h3><div class="row g-3">
<div class="col-md-4"><a class="card p-3 text-decoration-none" target="_blank" href="https://cybercrime.gov.in"><strong>National Cyber Crime Portal</strong><small>cybercrime.gov.in</small></a></div>
<div class="col-md-4"><a class="card p-3 text-decoration-none" target="_blank" href="https://sancharsaathi.gov.in"><strong>Sanchar Sathi Portal</strong><small>sancharsaathi.gov.in</small></a></div>
<div class="col-md-4"><a class="card p-3 text-decoration-none" target="_blank" href="https://mppolice.gov.in"><strong>MP Police Portal</strong><small>mppolice.gov.in</small></a></div>
</div></section>
<section class="container py-4"><h3>Latest News & Media Coverage</h3><div class="row g-3"><?php foreach($news as $n): ?><div class="col-md-4"><div class="card h-100"><img src="<?= e($n['thumbnail'] ?: '/assets/logo.svg') ?>" class="card-img-top" style="height:160px;object-fit:cover" alt="thumb"><div class="card-body"><h6><?= e($n['title']) ?></h6><small class="text-muted"><?= e($n['event_type']) ?> | <?= e((string)$n['event_date']) ?></small></div></div></div><?php endforeach; ?></div></section>
<section class="container py-4"><h3>Video Gallery</h3><div class="row g-3"><?php foreach($videos as $v): ?><div class="col-md-4"><div class="card h-100"><img src="<?= e($v['thumbnail'] ?: '/assets/logo.svg') ?>" class="card-img-top" style="height:160px;object-fit:cover" alt="thumb"><div class="card-body"><h6><?= e($v['title']) ?></h6><small class="text-muted"><?= e(substr((string)$v['created_at'],0,10)) ?></small><?php if(!empty($v['video_url'])): ?><div><a target="_blank" href="<?= e($v['video_url']) ?>">Watch Video</a></div><?php endif; ?></div></div></div><?php endforeach; ?></div></section>
<section class="container pb-5"><a class="btn btn-primary" href="/leaderboard.php">View Leaderboard</a> <a class="btn btn-dark" href="/tools.php">Cyber Security Tools</a> <a class="btn btn-secondary" href="/courses.php">Cyber Courses</a></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
