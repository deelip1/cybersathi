<?php require_once __DIR__ . '/config/db.php'; include __DIR__ . '/includes/header.php';
$courses=$pdo->query('SELECT * FROM courses ORDER BY id DESC')->fetchAll();
?>
<div class="container py-5"><h2>Cyber Awareness Courses</h2><div class="row g-3">
<?php foreach($courses as $c): ?><div class="col-md-6"><div class="card p-3"><h5><?= e($c['title']) ?></h5><p><?= e($c['description']) ?></p><a class="btn btn-sm btn-outline-primary" href="<?= e($c['video_url']) ?>">Video Lesson</a> <a class="btn btn-sm btn-outline-secondary" href="<?= e($c['pdf_url']) ?>">PDF Material</a></div></div><?php endforeach; ?>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
