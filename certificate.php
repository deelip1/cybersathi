<?php
require_once __DIR__ . '/config/db.php';
$id=(int)($_GET['result_id'] ?? 0);
$stmt=$pdo->prepare('SELECT * FROM quiz_results WHERE id=?');
$stmt->execute([$id]);
$r=$stmt->fetch();
if(!$r){ http_response_code(404); echo 'Result not found'; exit; }
?><!doctype html><html><head><meta charset="utf-8"><title>Certificate</title><style>body{font-family:Arial;background:#f7fbff}.c{margin:30px auto;border:8px solid #1d4ed8;padding:40px;max-width:900px;text-align:center;background:#fff}h1{color:#1e3a8a}</style></head><body>
<div class="c">
<img src="/assets/logo.svg" width="80" alt="logo">
<h1>Cyber Awareness Champion</h1>
<p>This certifies that</p>
<h2><?= e($r['participant_name']) ?></h2>
<p>has successfully completed Cyber Sathi Quiz with score <strong><?= (int)$r['score'] ?>/<?= (int)$r['total_questions'] ?></strong>.</p>
<p>Founder: Deelip Jaiswal</p>
<img src="/assets/sign.svg" width="200" alt="sign"><br>
<button onclick="window.print()">Download / Print PDF</button>
</div>
</body></html>
