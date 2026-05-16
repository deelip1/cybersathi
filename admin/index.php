<?php
require_once __DIR__ . '/../config/db.php';
ensure_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !validate_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(419);
    exit('Invalid CSRF token');
}

function normalize_severity(string $value): string
{
    $allowed = ['low', 'medium', 'high'];
    $value = strtolower(trim($value));
    return in_array($value, $allowed, true) ? $value : 'low';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_volunteer'])) {
    $volunteerId = (int)($_POST['approve_volunteer'] ?? 0);
    if ($volunteerId > 0) {
        $pdo->prepare('UPDATE volunteers SET approval_status = "approved" WHERE id = ?')->execute([$volunteerId]);
        $vs = $pdo->prepare('SELECT id, name, email FROM volunteers WHERE id = ?');
        $vs->execute([$volunteerId]);
        $volunteer = $vs->fetch();

        if ($volunteer) {
            $smtp = get_email_settings($pdo);
            $body = 'Dear ' . e($volunteer['name']) . ',<br>Welcome to Cyber Sathi.<br><br>Your account has been approved.<br><br>'
                . 'Please sign in using your registered email and the password you set during volunteer registration.<br><br>'
                . 'Login Here: <a href="' . OFFICIAL_DOMAIN . 'volunteer-login.php">Volunteer Login</a><br><br>Regards<br>Cyber Sathi Team';
            send_mail_smart($smtp, $volunteer['email'], $volunteer['name'], 'Welcome to Cyber Sathi', $body);
        }
    }

    header('Location: /admin/index.php#volunteers');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_case'])) {
    $pdo->prepare('UPDATE complaints SET assigned_to = ?, status = "in_progress" WHERE id = ?')
        ->execute([(int)$_POST['volunteer_id'], (int)$_POST['complaint_id']]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $pdo->prepare('INSERT INTO events(event_type, title, description, event_date, location) VALUES(?, ?, ?, ?, ?)')
        ->execute([
            trim((string)$_POST['event_type']),
            trim((string)$_POST['title']),
            trim((string)$_POST['description']),
            (string)$_POST['event_date'],
            trim((string)$_POST['location']),
        ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_alert'])) {
    $pdo->prepare('INSERT INTO fraud_alerts(title, message, severity, is_active) VALUES(?, ?, ?, 1)')
        ->execute([
            trim((string)$_POST['title']),
            trim((string)$_POST['message']),
            normalize_severity((string)$_POST['severity']),
        ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_email_settings'])) {
    $pdo->prepare('INSERT INTO email_settings(smtp_host, smtp_port, smtp_username, smtp_password, encryption, sender_email, sender_name) VALUES(?, ?, ?, ?, ?, ?, ?)')
        ->execute([
            trim((string)$_POST['smtp_host']),
            (int)$_POST['smtp_port'],
            trim((string)$_POST['smtp_username']),
            trim((string)$_POST['smtp_password']),
            trim((string)$_POST['encryption']),
            trim((string)$_POST['sender_email']),
            trim((string)$_POST['sender_name']),
        ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {
    if (!is_super_admin_logged_in()) {
        http_response_code(403);
        exit('Only super admin can create admins');
    }

    $pdo->prepare('INSERT INTO admin(name, email, password_hash, role) VALUES(?, ?, ?, ?)')
        ->execute([
            trim((string)$_POST['name']),
            trim((string)$_POST['email']),
            password_hash((string)$_POST['password'], PASSWORD_BCRYPT),
            trim((string)$_POST['role']) === 'super_admin' ? 'super_admin' : 'admin',
        ]);
}

$qall = static function (string $sql) use ($pdo): array {
    try {
        return $pdo->query($sql)->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
};
$qscalar = static function (string $sql) use ($pdo): int {
    try {
        return (int)$pdo->query($sql)->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
};

$complaints = $qall('SELECT * FROM complaints ORDER BY id DESC');
$pending = $qall('SELECT * FROM volunteers WHERE approval_status = "pending"');
$approved = $qall('SELECT * FROM volunteers WHERE approval_status = "approved"');
$usersRows = $qall('SELECT id, name, email, city, is_verified FROM users ORDER BY id DESC LIMIT 20');
$alerts = $qall('SELECT * FROM fraud_alerts ORDER BY id DESC LIMIT 10');
$admins = $qall('SELECT id, name, email, role FROM admin ORDER BY id DESC');
$emails = $qall('SELECT * FROM email_logs ORDER BY id DESC LIMIT 10');
$leaderboard = $qall('SELECT participant_name, MAX(score) AS score FROM quiz_results GROUP BY participant_name ORDER BY score DESC LIMIT 10');
$feedbackRows = $qall('SELECT * FROM feedback ORDER BY id DESC LIMIT 20');
$patternRows = $qall('SELECT fraud_type, COUNT(*) AS c FROM complaints GROUP BY fraud_type ORDER BY c DESC LIMIT 5');
$emailSettings = get_email_settings($pdo);

include __DIR__ . '/../includes/header.php';
?>
<div class="container py-4"><div class="wp-shell">
<aside class="wp-sidebar p-3">
<h5 class="mb-3">Cyber Sathi Admin</h5>
<a class="d-block text-white mb-2" href="#dashboard">Dashboard</a><a class="d-block text-white mb-2" href="#users">Users</a><a class="d-block text-white mb-2" href="#volunteers">Volunteers</a><a class="d-block text-white mb-2" href="#complaints">Complaints</a><a class="d-block text-white mb-2" href="#alerts">Fraud Alerts</a><a class="d-block text-white mb-2" href="#leaderboard">Leaderboard</a><a class="d-block text-white mb-2" href="#email">Email Communication</a><a class="d-block text-white mb-2" href="#email-settings">Email Settings</a><a class="d-block text-white mb-2" href="#feedback">Feedback</a><a class="d-block text-white mb-2" href="#patterns">Fraud Patterns</a><a class="d-block text-white mb-2" href="#media">Media</a><a class="d-block text-white mb-2" href="#settings">Settings</a>
<hr class="text-white-50"><small class="text-white-50">Logged in as <?= e($_SESSION['admin_name'] ?? 'Admin') ?></small><span class="badge bg-info text-dark mt-2"><?= e($_SESSION['admin_role'] ?? 'admin') ?></span><a class="btn btn-sm btn-outline-light mt-3" href="/admin/logout.php">Logout</a>
</aside>
<section class="wp-main p-4">
<h2 id="dashboard">Dashboard</h2>
<div class="row g-3 mb-4"><div class="col-md-2"><div class="card p-3"><strong><?= $qscalar('SELECT COUNT(*) FROM users') ?></strong><span>Total Users</span></div></div><div class="col-md-2"><div class="card p-3"><strong><?= count($complaints) ?></strong><span>Total Complaints</span></div></div><div class="col-md-2"><div class="card p-3"><strong><?= count($approved) ?></strong><span>Active Volunteers</span></div></div><div class="col-md-2"><div class="card p-3"><strong><?= $qscalar('SELECT COUNT(*) FROM quiz_results') ?></strong><span>Quiz Participants</span></div></div><div class="col-md-2"><div class="card p-3"><strong><?= $qscalar('SELECT COUNT(*) FROM quiz_results') ?></strong><span>Certificates</span></div></div><div class="col-md-2"><div class="card p-3"><strong><?= $qscalar('SELECT COUNT(*) FROM fraud_alerts WHERE is_active=1') ?></strong><span>Fraud Alerts</span></div></div></div>

<h4 id="users">Recent Users</h4><div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>City</th><th>Verified</th></tr></thead><tbody><?php foreach($usersRows as $u): ?><tr><td><?= (int)$u['id'] ?></td><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['city']) ?></td><td><?= (int)$u['is_verified']===1?'Yes':'No' ?></td></tr><?php endforeach; ?></tbody></table></div>

<h4 id="volunteers" class="mt-4">Approve Volunteers</h4><?php foreach($pending as $p): ?><div class="card p-2 mb-2"><?= e($p['name']) ?> (<?= e($p['email']) ?>) <form method="post" class="d-inline"><?= csrf_input() ?><button class="btn btn-sm btn-success" type="submit" name="approve_volunteer" value="<?= (int)$p['id'] ?>">Approve & Send Welcome Email</button></form></div><?php endforeach; ?>
<h4 id="complaints" class="mt-4">Assign Complaints</h4><form method="post" class="row g-2"><?= csrf_input() ?><input type="hidden" name="assign_case" value="1"><div class="col-md-5"><select name="complaint_id" class="form-select"><?php foreach($complaints as $c): ?><option value="<?= (int)$c['id'] ?>">#<?= (int)$c['id'] ?> <?= e($c['user_name']) ?> - <?= e($c['fraud_type']) ?></option><?php endforeach; ?></select></div><div class="col-md-5"><select name="volunteer_id" class="form-select"><?php foreach($approved as $v): ?><option value="<?= (int)$v['id'] ?>"><?= e($v['name']) ?></option><?php endforeach; ?></select></div><div class="col-md-2"><button class="btn btn-primary w-100">Assign</button></div></form>

<h4 id="alerts" class="mt-4">Live Fraud Alert System</h4><form method="post" class="row g-2"><?= csrf_input() ?><input type="hidden" name="create_alert" value="1"><div class="col-md-3"><input name="title" class="form-control" placeholder="Alert Title" required></div><div class="col-md-5"><input name="message" class="form-control" placeholder="Alert message" required></div><div class="col-md-2"><select name="severity" class="form-select"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select></div><div class="col-md-2"><button class="btn btn-danger w-100">Publish</button></div></form><?php foreach($alerts as $a): ?><div class="card p-2 mt-2">[<?= e($a['severity']) ?>] <?= e($a['title']) ?> - <?= e($a['message']) ?></div><?php endforeach; ?>

<h4 id="email" class="mt-4">Email Communication (PHPMailer)</h4><form method="post" action="/api/send_email.php" class="row g-2"><?= csrf_input() ?><div class="col-md-3"><select name="recipient_type" class="form-select"><option value="single">Single User</option><option value="users">Bulk Users</option><option value="volunteers">Volunteers</option><option value="participants">Quiz Participants</option></select></div><div class="col-md-3"><input name="subject" class="form-control" placeholder="Subject" required></div><div class="col-md-4"><input name="message" class="form-control" placeholder="Message" required></div><div class="col-md-2"><button class="btn btn-dark w-100">Send/Queue</button></div></form><div class="table-responsive mt-2"><table class="table table-sm"><thead><tr><th>Subject</th><th>Target</th><th>Count</th><th>Status</th></tr></thead><tbody><?php foreach($emails as $m): ?><tr><td><?= e($m['subject']) ?></td><td><?= e($m['recipient_type']) ?></td><td><?= (int)$m['recipients_count'] ?></td><td><?= e($m['status']) ?></td></tr><?php endforeach; ?></tbody></table></div>

<h4 id="email-settings" class="mt-4">Email Settings</h4><form method="post" class="row g-2"><?= csrf_input() ?><input type="hidden" name="save_email_settings" value="1"><div class="col-md-4"><input name="smtp_host" class="form-control" placeholder="SMTP Host" value="<?= e($emailSettings['smtp_host'] ?? '') ?>"></div><div class="col-md-2"><input name="smtp_port" class="form-control" type="number" value="<?= e((string)($emailSettings['smtp_port'] ?? 587)) ?>"></div><div class="col-md-3"><input name="smtp_username" class="form-control" placeholder="SMTP Username" value="<?= e($emailSettings['smtp_username'] ?? '') ?>"></div><div class="col-md-3"><input name="smtp_password" class="form-control" placeholder="SMTP Password" value="<?= e($emailSettings['smtp_password'] ?? '') ?>"></div><div class="col-md-2"><select name="encryption" class="form-select"><option value="tls" <?= (($emailSettings['encryption'] ?? 'tls')==='tls')?'selected':'' ?>>TLS</option><option value="ssl" <?= (($emailSettings['encryption'] ?? '')==='ssl')?'selected':'' ?>>SSL</option></select></div><div class="col-md-5"><input name="sender_email" class="form-control" placeholder="Sender Email" value="<?= e($emailSettings['sender_email'] ?? '') ?>"></div><div class="col-md-4"><input name="sender_name" class="form-control" placeholder="Sender Name" value="<?= e($emailSettings['sender_name'] ?? '') ?>"></div><div class="col-md-1"><button class="btn btn-success w-100">Save</button></div></form>

<h4 id="feedback" class="mt-4">User Feedback</h4><div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr></thead><tbody><?php foreach($feedbackRows as $f): ?><tr><td><?= e($f['name']) ?></td><td><?= e($f['email']) ?></td><td><?= e($f['message']) ?></td><td><?= e($f['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div>

<h4 id="patterns" class="mt-4">Fraud Pattern Learning (Top Types)</h4><?php foreach($patternRows as $p): ?><div class="card p-2 mb-1"><?= e($p['fraud_type'] ?: 'Unknown') ?> <span class="badge bg-danger"><?= (int)$p['c'] ?></span></div><?php endforeach; ?>

<h4 id="leaderboard" class="mt-4">Leaderboard (Top 10)</h4><?php foreach($leaderboard as $i=>$l): ?><div><?= $i+1 ?>. <?= e($l['participant_name']) ?> - <?= (int)$l['score'] ?></div><?php endforeach; ?>

<h4 id="media" class="mt-4">Seminars / Webinars / Media Upload</h4><form method="post" class="row g-2"><?= csrf_input() ?><input type="hidden" name="create_event" value="1"><div class="col-md-2"><select name="event_type" class="form-select"><option>Seminar</option><option>Webinar</option><option>News</option><option>Media</option></select></div><div class="col-md-3"><input name="title" class="form-control" required placeholder="Title"></div><div class="col-md-3"><input name="location" class="form-control" placeholder="Location"></div><div class="col-md-2"><input type="date" name="event_date" class="form-control" required></div><div class="col-md-2"><button class="btn btn-success w-100">Publish</button></div><div class="col-12"><textarea name="description" class="form-control" required></textarea></div></form>

<h4 id="settings" class="mt-4">Super Admin Settings</h4><div class="table-responsive mb-3"><table class="table table-striped"><thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead><tbody><?php foreach($admins as $a): ?><tr><td><?= e($a['name']) ?></td><td><?= e($a['email']) ?></td><td><?= e($a['role']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php if(is_super_admin_logged_in()): ?><form method="post" class="row g-2"><?= csrf_input() ?><input type="hidden" name="create_admin" value="1"><div class="col-md-3"><input name="name" class="form-control" required placeholder="Admin name"></div><div class="col-md-3"><input name="email" class="form-control" type="email" required placeholder="Admin email"></div><div class="col-md-3"><input name="password" class="form-control" type="password" required placeholder="Password"></div><div class="col-md-2"><select name="role" class="form-select"><option value="admin">Admin</option><option value="super_admin">Super Admin</option></select></div><div class="col-md-1"><button class="btn btn-dark w-100">Add</button></div></form><?php endif; ?>
</section></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
