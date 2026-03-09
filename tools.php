<?php require_once __DIR__ . '/config/db.php'; include __DIR__ . '/includes/header.php';
$result='';$risk='';$sms='';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['sms_text'])){
  $sms=trim($_POST['sms_text']);
  $keywords=['otp','investment','loan','task','urgent','kyc','refund','reward'];
  $score=0; foreach($keywords as $k){ if(stripos($sms,$k)!==false) $score++; }
  $risk=$score>=3?'High':($score>=1?'Medium':'Low');
  $result="SMS Scam Risk: $risk";
}
$phones=$pdo->query('SELECT phone,fraud_type,report_count FROM fraud_phone_reports ORDER BY report_count DESC LIMIT 20')->fetchAll();
?>
<div class="container py-5">
<h2>Advanced Cyber Security Tools</h2><?php if(isset($_GET['website_status'])): ?><div class="alert alert-info">Website Check (<?= e($_GET['website_url'] ?? '') ?>): <strong><?= e($_GET['website_status']) ?></strong></div><?php endif; ?>
<div class="row g-4">
<div class="col-md-6"><div class="card p-3"><h5>SMS Scam Detection Tool</h5><form method="post"><?= csrf_input() ?><textarea name="sms_text" class="form-control" rows="5" placeholder="Paste suspicious SMS" required><?= e($sms) ?></textarea><button class="btn btn-primary mt-2">Analyze</button></form><?php if($result): ?><div class="alert alert-warning mt-2"><?= e($result) ?></div><?php endif; ?></div></div>
<div class="col-md-6"><div class="card p-3"><h5>WhatsApp Scam Detection</h5><p>Use same analyzer for WhatsApp text to detect fake jobs, investment scams, and OTP fraud.</p><p class="text-muted">Tip: look for urgency + payment request + unknown links.</p></div></div>
<div class="col-md-6"><div class="card p-3"><h5>Fake Website Checker</h5><form method="post" action="/api/website_check.php"><?= csrf_input() ?><input name="url" class="form-control" placeholder="https://example.com" required><button class="btn btn-dark mt-2">Check Website</button></form></div></div>
<div class="col-md-6"><div class="card p-3"><h5>Fraud Phone Database</h5><form method="post" action="/api/report_phone.php" class="row g-2"><?= csrf_input() ?><div class="col-6"><input name="phone" class="form-control" placeholder="Phone" required></div><div class="col-6"><input name="fraud_type" class="form-control" placeholder="Fraud Type" required></div><div class="col-12"><button class="btn btn-danger">Report Number</button></div></form><hr><?php foreach($phones as $p): ?><div><?= e($p['phone']) ?> - <?= e($p['fraud_type']) ?> (Reports: <?= (int)$p['report_count'] ?>)</div><?php endforeach; ?></div></div>
</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
