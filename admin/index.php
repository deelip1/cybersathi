<?php require_once __DIR__ . '/../config/db.php'; ensure_admin();

if(isset($_GET['approve_volunteer'])){
  if(!validate_csrf_token($_GET['csrf_token'] ?? null)){ http_response_code(419); exit('Invalid CSRF token'); }
  $pdo->prepare('UPDATE volunteers SET approval_status="approved" WHERE id=?')->execute([(int)$_GET['approve_volunteer']]);
  header('Location: /admin/index.php'); exit;
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['assign_case'])){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){ http_response_code(419); exit('Invalid CSRF token'); }
  $pdo->prepare('UPDATE complaints SET assigned_to=?, status="in_progress" WHERE id=?')->execute([(int)$_POST['volunteer_id'], (int)$_POST['complaint_id']]);
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['create_event'])){
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){ http_response_code(419); exit('Invalid CSRF token'); }
  $pdo->prepare('INSERT INTO events(event_type,title,description,event_date,location) VALUES(?,?,?,?,?)')->execute([$_POST['event_type'],$_POST['title'],$_POST['description'],$_POST['event_date'],$_POST['location']]);
}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['create_admin'])){
  if(!is_super_admin_logged_in()){ http_response_code(403); exit('Only super admin can create admins'); }
  if(!validate_csrf_token($_POST['csrf_token'] ?? null)){ http_response_code(419); exit('Invalid CSRF token'); }
  $pdo->prepare('INSERT INTO admin(name,email,password_hash,role) VALUES(?,?,?,?)')->execute([
    trim($_POST['name']), trim($_POST['email']), password_hash($_POST['password'], PASSWORD_BCRYPT), trim($_POST['role']) === 'super_admin' ? 'super_admin' : 'admin'
  ]);
}

$complaints=$pdo->query('SELECT * FROM complaints ORDER BY id DESC')->fetchAll();
$pending=$pdo->query('SELECT * FROM volunteers WHERE approval_status="pending"')->fetchAll();
$approved=$pdo->query('SELECT * FROM volunteers WHERE approval_status="approved"')->fetchAll();
$qcount=(int)$pdo->query('SELECT COUNT(*) FROM quiz_questions')->fetchColumn();
$users=(int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$events=(int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
$admins=$pdo->query('SELECT id,name,email,role FROM admin ORDER BY id DESC')->fetchAll();

include __DIR__ . '/../includes/header.php'; ?>
<div class="container py-4">
  <div class="wp-shell">
    <aside class="wp-sidebar p-3">
      <h5 class="mb-3">Super Control</h5>
      <a class="d-block text-white mb-2" href="#overview">Overview</a>
      <a class="d-block text-white mb-2" href="#volunteers">Volunteers</a>
      <a class="d-block text-white mb-2" href="#complaints">Complaints</a>
      <a class="d-block text-white mb-2" href="#events">Events</a>
      <a class="d-block text-white mb-2" href="#admins">Admin Users</a>
      <hr class="text-white-50">
      <small class="text-white-50">Logged in as</small>
      <div class="fw-semibold"><?= e($_SESSION['admin_name'] ?? 'Admin') ?></div>
      <span class="badge bg-info text-dark mt-2"><?= e($_SESSION['admin_role'] ?? 'admin') ?></span>
      <a class="btn btn-sm btn-outline-light mt-3" href="/admin/logout.php">Logout</a>
    </aside>

    <section class="wp-main p-4">
      <h2 id="overview">Admin Dashboard</h2>
      <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card p-3"><strong><?= count($complaints) ?></strong><span>Complaints</span></div></div>
        <div class="col-md-3"><div class="card p-3"><strong><?= count($pending) ?></strong><span>Pending Volunteers</span></div></div>
        <div class="col-md-3"><div class="card p-3"><strong><?= $users ?></strong><span>Users</span></div></div>
        <div class="col-md-3"><div class="card p-3"><strong><?= $qcount ?></strong><span>Quiz Questions</span></div></div>
      </div>

      <h4 id="volunteers">Approve Volunteers</h4>
      <?php foreach($pending as $p): ?><div class="card p-2 mb-2"><?= e($p['name']) ?> (<?= e($p['email']) ?>) <a class="btn btn-sm btn-success" href="?approve_volunteer=<?= (int)$p['id'] ?>&csrf_token=<?= e(csrf_token()) ?>">Approve</a></div><?php endforeach; ?>

      <h4 id="complaints" class="mt-4">Assign Complaints</h4>
      <form method="post" class="row g-2">
        <?= csrf_input() ?>
        <input type="hidden" name="assign_case" value="1">
        <div class="col-md-5"><select name="complaint_id" class="form-select"><?php foreach($complaints as $c): ?><option value="<?= (int)$c['id'] ?>">#<?= (int)$c['id'] ?> <?= e($c['user_name']) ?> - <?= e($c['fraud_type']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-5"><select name="volunteer_id" class="form-select"><?php foreach($approved as $v): ?><option value="<?= (int)$v['id'] ?>"><?= e($v['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Assign</button></div>
      </form>

      <h4 id="events" class="mt-4">Publish Seminar / Webinar / News</h4>
      <form method="post" class="row g-2">
        <?= csrf_input() ?>
        <input type="hidden" name="create_event" value="1">
        <div class="col-md-2"><select name="event_type" class="form-select"><option>Seminar</option><option>Webinar</option><option>News</option><option>Media</option></select></div>
        <div class="col-md-3"><input name="title" class="form-control" required placeholder="Title"></div>
        <div class="col-md-3"><input name="location" class="form-control" placeholder="Location"></div>
        <div class="col-md-2"><input type="date" name="event_date" class="form-control" required></div>
        <div class="col-md-2"><button class="btn btn-success w-100">Publish</button></div>
        <div class="col-12"><textarea name="description" class="form-control" required></textarea></div>
      </form>

      <h4 id="admins" class="mt-4">Admin Accounts (Super Admin Control)</h4>
      <div class="table-responsive mb-3">
        <table class="table table-striped">
          <thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
          <tbody><?php foreach($admins as $a): ?><tr><td><?= e($a['name']) ?></td><td><?= e($a['email']) ?></td><td><span class="badge bg-secondary"><?= e($a['role'] ?? 'admin') ?></span></td></tr><?php endforeach; ?></tbody>
        </table>
      </div>

      <?php if(is_super_admin_logged_in()): ?>
      <form method="post" class="row g-2">
        <?= csrf_input() ?>
        <input type="hidden" name="create_admin" value="1">
        <div class="col-md-3"><input name="name" class="form-control" required placeholder="Admin name"></div>
        <div class="col-md-3"><input name="email" class="form-control" type="email" required placeholder="Admin email"></div>
        <div class="col-md-3"><input name="password" class="form-control" type="password" required placeholder="Password"></div>
        <div class="col-md-2"><select name="role" class="form-select"><option value="admin">Admin</option><option value="super_admin">Super Admin</option></select></div>
        <div class="col-md-1"><button class="btn btn-dark w-100">Add</button></div>
      </form>
      <?php else: ?>
      <div class="alert alert-warning">Only super admin can create or promote admin users.</div>
      <?php endif; ?>
    </section>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
