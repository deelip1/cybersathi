<?php require_once __DIR__ . '/config/db.php'; ensure_user(); include __DIR__ . '/includes/header.php';
$stmt=$pdo->prepare('SELECT * FROM quiz_results WHERE user_id=? ORDER BY id DESC');
$stmt->execute([$_SESSION['user_id']]);
$results=$stmt->fetchAll();
?>
<div class="container py-5">
  <h2>User Dashboard</h2>
  <p>Welcome, <?= e($_SESSION['user_name']) ?> | <a href="/logout.php">Logout</a></p>
  <h4>Your Quiz Results</h4>
  <?php if(!$results): ?><div class="alert alert-info">No quiz attempts yet.</div><?php endif; ?>
  <?php foreach($results as $r): ?>
    <div class="card p-3 mb-2">
      Score: <strong><?= (int)$r['score'] ?>/<?= (int)$r['total_questions'] ?></strong>
      <a class="btn btn-sm btn-outline-primary ms-2" href="/certificate.php?result_id=<?= (int)$r['id'] ?>&download=1">Download Certificate PDF</a>
    </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
