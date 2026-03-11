<?php require_once __DIR__ . '/config/db.php'; ensure_user(); include __DIR__ . '/includes/header.php';
$order = ($dbDriver ?? 'mysql') === 'sqlite' ? 'RANDOM()' : 'RAND()';
$q=$pdo->query("SELECT * FROM quiz_questions ORDER BY $order LIMIT 10")->fetchAll();
$u=$pdo->prepare('SELECT email,city FROM users WHERE id=?');
$u->execute([$_SESSION['user_id']]);
$userMeta=$u->fetch() ?: ['email'=>'','city'=>''];
?>
<div class="container py-5">
  <h2>Cyber Awareness Quiz</h2>
  <?php if(isset($_GET['welcome'])): ?><div class="alert alert-success"><?= e($_GET['welcome']) ?></div><?php endif; ?>
  <p id="timer" class="fw-bold text-danger">Timer: 300s</p>
  <form method="post" action="/api/quiz_submit.php" id="quizForm"><?= csrf_input() ?>
    <input type="hidden" name="participant_name" value="<?= e($_SESSION['user_name'] ?? 'Participant') ?>">
    <input type="hidden" name="participant_email" value="<?= e($userMeta['email'] ?? '') ?>">
    <input type="hidden" name="participant_city" value="<?= e($userMeta['city'] ?? '') ?>">
    <?php foreach($q as $i=>$row): ?><div class="card p-3 mb-3"><p><strong>Q<?= $i+1 ?>.</strong> <?= e($row['question']) ?></p><?php for($o=1;$o<=4;$o++): $opt='option_'.$o; ?><label><input type="radio" name="q[<?= (int)$row['id'] ?>]" value="<?= $o ?>" required> <?= e($row[$opt]) ?></label><br><?php endfor; ?></div><?php endforeach; ?>
    <button class="btn btn-primary">Submit Quiz</button>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
