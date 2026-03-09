<?php require_once __DIR__ . '/config/db.php'; include __DIR__ . '/includes/header.php';
$rows=$pdo->query('SELECT qr.participant_name,u.city,MAX(qr.score) as best_score FROM quiz_results qr LEFT JOIN users u ON u.id=qr.user_id GROUP BY qr.participant_name,u.city ORDER BY best_score DESC LIMIT 50')->fetchAll();
?>
<div class="container py-5"><h2>Cyber Awareness Leaderboard</h2>
<table class="table table-striped"><thead><tr><th>Rank</th><th>Name</th><th>City</th><th>Score</th></tr></thead><tbody>
<?php foreach($rows as $i=>$r): ?><tr><td><?= $i+1 ?></td><td><?= e($r['participant_name']) ?></td><td><?= e($r['city'] ?? 'NA') ?></td><td><?= (int)$r['best_score'] ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
