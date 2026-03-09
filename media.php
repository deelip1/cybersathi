<?php require_once __DIR__ . '/config/db.php'; include __DIR__ . '/includes/header.php';
$events=$pdo->query('SELECT * FROM events ORDER BY event_date DESC')->fetchAll();
$videos=$pdo->query('SELECT * FROM videos ORDER BY id DESC')->fetchAll();
?>
<div class="container py-5">
  <h2>Media and Awareness</h2>
  <div class="row">
    <div class="col-md-6"><h4>Seminars/Webinars/News</h4><?php foreach($events as $ev): ?><div class="card p-3 mb-2"><strong><?= e($ev['title']) ?></strong><small><?= e($ev['event_type']) ?> - <?= e($ev['event_date']) ?></small><p><?= e($ev['description']) ?></p></div><?php endforeach; ?></div>
    <div class="col-md-6"><h4>Video Gallery</h4><?php foreach($videos as $v): ?><div class="card p-3 mb-2"><strong><?= e($v['title']) ?></strong><a target="_blank" href="<?= e($v['video_url']) ?>">Watch Video</a></div><?php endforeach; ?></div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
