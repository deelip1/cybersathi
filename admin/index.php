<?php require_once __DIR__ . '/../config/db.php'; ensure_admin();
if(isset($_GET['approve_volunteer'])){ $pdo->prepare('UPDATE volunteers SET approval_status="approved" WHERE id=?')->execute([(int)$_GET['approve_volunteer']]); header('Location: /admin/index.php'); exit; }
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['assign_case'])){
  $pdo->prepare('UPDATE complaints SET assigned_to=?, status="in_progress" WHERE id=?')->execute([(int)$_POST['volunteer_id'], (int)$_POST['complaint_id']]);
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['create_event'])){
  $pdo->prepare('INSERT INTO events(event_type,title,description,event_date,location) VALUES(?,?,?,?,?)')->execute([$_POST['event_type'],$_POST['title'],$_POST['description'],$_POST['event_date'],$_POST['location']]);
}
$complaints=$pdo->query('SELECT * FROM complaints ORDER BY id DESC')->fetchAll();
$pending=$pdo->query('SELECT * FROM volunteers WHERE approval_status="pending"')->fetchAll();
$approved=$pdo->query('SELECT * FROM volunteers WHERE approval_status="approved"')->fetchAll();
$qcount=$pdo->query('SELECT COUNT(*) FROM quiz_questions')->fetchColumn();
include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-4">
  <h2>Admin Dashboard</h2>
  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card p-3">Complaints: <?= count($complaints) ?></div></div>
    <div class="col-md-3"><div class="card p-3">Pending Volunteers: <?= count($pending) ?></div></div>
    <div class="col-md-3"><div class="card p-3">Approved Volunteers: <?= count($approved) ?></div></div>
    <div class="col-md-3"><div class="card p-3">Quiz Questions: <?= (int)$qcount ?></div></div>
  </div>

  <h4>Approve Volunteers</h4>
  <?php foreach($pending as $p): ?><div class="card p-2 mb-2"><?= e($p['name']) ?> (<?= e($p['email']) ?>) <a class="btn btn-sm btn-success" href="?approve_volunteer=<?= (int)$p['id'] ?>">Approve</a></div><?php endforeach; ?>

  <h4 class="mt-4">Assign Complaints</h4>
  <form method="post" class="row g-2">
    <input type="hidden" name="assign_case" value="1">
    <div class="col-md-5"><select name="complaint_id" class="form-select"><?php foreach($complaints as $c): ?><option value="<?= (int)$c['id'] ?>">#<?= (int)$c['id'] ?> <?= e($c['user_name']) ?> - <?= e($c['fraud_type']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-5"><select name="volunteer_id" class="form-select"><?php foreach($approved as $v): ?><option value="<?= (int)$v['id'] ?>"><?= e($v['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Assign</button></div>
  </form>

  <h4 class="mt-4">Add Seminar/Webinar/News</h4>
  <form method="post" class="row g-2">
    <input type="hidden" name="create_event" value="1">
    <div class="col-md-2"><select name="event_type" class="form-select"><option>Seminar</option><option>Webinar</option><option>News</option><option>Media</option></select></div>
    <div class="col-md-3"><input name="title" class="form-control" required placeholder="Title"></div>
    <div class="col-md-3"><input name="location" class="form-control" placeholder="Location"></div>
    <div class="col-md-2"><input type="date" name="event_date" class="form-control" required></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Publish</button></div>
    <div class="col-12"><textarea name="description" class="form-control" required></textarea></div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
