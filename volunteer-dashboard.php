<?php require_once __DIR__ . '/config/db.php'; ensure_volunteer(); include __DIR__ . '/includes/header.php';
$stmt=$pdo->prepare('SELECT c.* FROM complaints c WHERE c.assigned_to=? ORDER BY c.id DESC');
$stmt->execute([$_SESSION['volunteer_id']]);
$cases=$stmt->fetchAll();
?>
<div class="container py-5">
  <h2>Volunteer Dashboard</h2>
  <p>Welcome, <?= e($_SESSION['volunteer_name']) ?> | <a href="/volunteer-logout.php">Logout</a></p>
  <h4>Assigned Cases</h4>
  <?php if(!$cases): ?><div class="alert alert-info">No assigned cases yet.</div><?php endif; ?>
  <?php foreach($cases as $c): ?>
    <div class="card p-3 mb-2">
      <strong>#<?= (int)$c['id'] ?> <?= e($c['fraud_type']) ?></strong>
      <div>Victim: <?= e($c['user_name']) ?> (<?= e($c['mobile']) ?>)</div>
      <div>Status: <?= e($c['status']) ?></div>
      <p><?= e($c['description']) ?></p>
    </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
