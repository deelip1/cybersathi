<?php require_once __DIR__ . '/config/db.php'; include __DIR__ . '/includes/header.php';
$order = ($dbDriver ?? 'mysql') === 'sqlite' ? 'RANDOM()' : 'RAND()';
$q=$pdo->query("SELECT * FROM quiz_questions ORDER BY $order LIMIT 10")->fetchAll();
?>
<div class="container py-5">
  <h2>Cyber Awareness Quiz</h2>
  <?php if(isset($_GET['welcome'])): ?><div class="alert alert-success">Registration completed! Please play the quiz to receive your Cyber Sathi certificate.</div><?php endif; ?>
  <p id="timer" class="fw-bold text-danger">Timer: 300s</p>
  <form method="post" action="/api/quiz_submit.php" id="quizForm">
    <?= csrf_input() ?>
    <input type="hidden" name="participant_name" value="<?= e($_SESSION['user_name'] ?? 'Guest Participant') ?>">
    <?php foreach($q as $i=>$row): ?>
      <div class="card p-3 mb-3">
        <p><strong>Q<?= $i+1 ?>.</strong> <?= e($row['question']) ?></p>
        <?php for($o=1;$o<=4;$o++): $opt='option_'.$o; ?>
          <label><input type="radio" name="q[<?= (int)$row['id'] ?>]" value="<?= $o ?>" required> <?= e($row[$opt]) ?></label><br>
        <?php endfor; ?>
      </div>
    <?php endforeach; ?>
    <button class="btn btn-primary">Submit Quiz</button>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
