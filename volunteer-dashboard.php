<?php
require_once __DIR__ . '/config/db.php';
ensure_volunteer();

$volunteerId = (int)($_SESSION['volunteer_id'] ?? 0);
$volunteerName = (string)($_SESSION['volunteer_name'] ?? 'Volunteer');

if ($volunteerId <= 0) {
    http_response_code(403);
    exit('Invalid volunteer session');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !validate_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(419);
    exit('Invalid CSRF token');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_case_status'])) {
    $complaintId = (int)($_POST['complaint_id'] ?? 0);
    $nextStatus = (string)($_POST['status'] ?? '');
    $allowedStatuses = ['in_progress', 'resolved'];

    if ($complaintId > 0 && in_array($nextStatus, $allowedStatuses, true)) {
        $pdo->prepare('UPDATE complaints SET status = ? WHERE id = ? AND assigned_to = ?')
            ->execute([$nextStatus, $complaintId, $volunteerId]);
    }

    header('Location: /volunteer-dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT c.id, c.fraud_type, c.user_name, c.mobile, c.status, c.description, c.created_at FROM complaints c WHERE c.assigned_to = ? ORDER BY c.id DESC');
$stmt->execute([$volunteerId]);
$cases = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<div class="container py-5">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
    <div>
      <h2 class="mb-1">Volunteer Dashboard</h2>
      <p class="text-muted mb-0">Welcome, <strong><?= e($volunteerName) ?></strong></p>
    </div>
    <a class="btn btn-outline-danger btn-sm" href="/volunteer-logout.php">Logout</a>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap gap-3">
      <div><span class="badge bg-primary">Total Assigned: <?= count($cases) ?></span></div>
      <div><span class="badge bg-warning text-dark">In Progress: <?= count(array_filter($cases, static fn($row) => ($row['status'] ?? '') === 'in_progress')) ?></span></div>
      <div><span class="badge bg-success">Resolved: <?= count(array_filter($cases, static fn($row) => ($row['status'] ?? '') === 'resolved')) ?></span></div>
    </div>
  </div>

  <h4 class="mb-3">Assigned Cases</h4>
  <?php if (!$cases): ?>
    <div class="alert alert-info">No assigned cases yet.</div>
  <?php endif; ?>

  <?php foreach ($cases as $c): ?>
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap gap-2">
          <strong>#<?= (int)$c['id'] ?> <?= e($c['fraud_type']) ?></strong>
          <span class="badge <?= ($c['status'] ?? '') === 'resolved' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= e($c['status']) ?></span>
        </div>
        <div class="small text-muted mt-1">Reported: <?= e((string)($c['created_at'] ?? '')) ?></div>
        <div class="mt-2">Victim: <?= e($c['user_name']) ?> (<?= e($c['mobile']) ?>)</div>
        <p class="mt-2 mb-3"><?= nl2br(e($c['description'])) ?></p>

        <form method="post" class="row g-2 align-items-center">
          <?= csrf_input() ?>
          <input type="hidden" name="update_case_status" value="1">
          <input type="hidden" name="complaint_id" value="<?= (int)$c['id'] ?>">
          <div class="col-12 col-md-4">
            <select name="status" class="form-select form-select-sm">
              <option value="in_progress" <?= ($c['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
              <option value="resolved" <?= ($c['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
            </select>
          </div>
          <div class="col-12 col-md-3">
            <button class="btn btn-sm btn-primary w-100" type="submit">Update Status</button>
          </div>
        </form>
      </div>
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
