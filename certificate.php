<?php
require_once __DIR__ . '/config/db.php';
$id=(int)($_GET['result_id'] ?? 0);
$stmt=$pdo->prepare('SELECT qr.*,u.name as user_name,u.email as user_email,u.city as user_city FROM quiz_results qr LEFT JOIN users u ON u.id=qr.user_id WHERE qr.id=?');
$stmt->execute([$id]);
$r=$stmt->fetch();
if(!$r){http_response_code(404);exit('Result not found');}

$name=$r['user_name'] ?: $r['participant_name'];
$email=($r['user_email'] ?: ($r['participant_email'] ?? '')) ?: 'N/A';
$city=($r['user_city'] ?: ($r['participant_city'] ?? '')) ?: 'N/A';
$certNo='CS-'.str_pad((string)$id,6,'0',STR_PAD_LEFT);
$issue=date('d M Y');
$verifyUrl=OFFICIAL_DOMAIN.'certificate.php?result_id='.$id;
$qrImg='https://api.qrserver.com/v1/create-qr-code/?size=120x120&data='.urlencode($verifyUrl);

if(isset($_GET['download'])){
  $autoload=__DIR__.'/vendor/autoload.php';
  if(file_exists($autoload)) require_once $autoload;
  if(class_exists('TCPDF')){
    $pdf=new TCPDF('L','mm','A4');
    $pdf->SetCreator(APP_NAME);
    $pdf->SetAuthor(FOUNDER_NAME);
    $pdf->SetTitle('Cyber Awareness Champion');
    $pdf->AddPage();
    $html='<div style="border:6px solid #1e3a8a;padding:20px;text-align:center;">'
      .'<h1 style="color:#1e3a8a;letter-spacing:2px;">CYBER AWARENESS CHAMPION</h1>'
      .'<p style="font-size:26px;opacity:.15;">CYBER SATHI</p>'
      .'<p>Name: '.e($name).'<br>Email: '.e($email).'<br>City: '.e($city)
      .'<br>Score: '.(int)$r['score'].'/'.(int)$r['total_questions']
      .'<br>Certificate ID: '.e($certNo).'<br>Issue Date: '.e($issue)
      .'<br>Founder: Deelip Jaiswal</p>'
      .'</div>';
    $pdf->writeHTML($html,true,false,true,false,'');
    $pdf->Output('cyber-certificate-'.$certNo.'.pdf','D');
    exit;
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Cyber Certificate</title>
<style>
body{font-family:Georgia,serif;background:radial-gradient(circle,#f0f9ff,#dbeafe);padding:20px}.cert{max-width:1100px;margin:0 auto;background:#fff;border:12px solid #1e3a8a;position:relative;box-shadow:0 12px 30px rgba(0,0,0,.15)}
.water{position:absolute;inset:0;display:flex;justify-content:center;align-items:center;font-size:88px;color:rgba(30,58,138,.07);font-weight:700;pointer-events:none}
.inner{position:relative;padding:32px;border:2px dashed #2563eb;margin:14px;text-align:center}.meta{color:#334155;font-size:14px}.row{display:flex;justify-content:space-between;align-items:flex-end;margin-top:20px}
.btn{display:inline-block;margin-top:12px;background:#1e40af;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px}
</style></head><body>
<div class="cert"><div class="water">CYBER SATHI</div><div class="inner">
<img src="/assets/logo.svg" width="90" alt="logo"><h1 style="color:#1e3a8a;letter-spacing:2px">CYBER AWARENESS CHAMPION</h1>
<p><strong>Cyber Sathi Certificate of Achievement</strong></p><p>This certifies that</p><h2><?= e($name) ?></h2>
<p class="meta">Email: <?= e($email) ?> | City: <?= e($city) ?> | Score: <?= (int)$r['score'] ?>/<?= (int)$r['total_questions'] ?></p>
<p class="meta">Certificate ID: <?= e($certNo) ?> | Issue Date: <?= e($issue) ?></p>
<div class="row"><div><strong>Founder:</strong> Deelip Jaiswal<br><img src="/assets/sign.svg" width="170" alt="sign"></div><div><img src="<?= e($qrImg) ?>" width="120" height="120" alt="QR"><br><small>Verify Certificate</small></div></div>
<a class="btn" href="?result_id=<?= (int)$id ?>&download=1">Download PDF</a>
</div></div>
</body></html>

$stmt=$pdo->prepare('SELECT qr.*, u.email AS user_email, u.mobile AS user_mobile, u.city AS user_city, u.name AS user_name FROM quiz_results qr LEFT JOIN users u ON u.id = qr.user_id WHERE qr.id=?');
$stmt->execute([$id]);
$r=$stmt->fetch();
if(!$r){ http_response_code(404); echo 'Result not found'; exit; }

$participantName = (string)($r['user_name'] ?? $r['participant_name'] ?? 'Participant');
$email = (string)($r['user_email'] ?? 'Not Available');
$mobile = (string)($r['user_mobile'] ?? 'Not Available');
$city = (string)($r['user_city'] ?? 'Not Available');
$certNo = 'CS-' . str_pad((string)$id, 6, '0', STR_PAD_LEFT);
$issuedOn = date('d M Y');

$download = isset($_GET['download']);
if ($download) {
    $autoload = __DIR__ . '/vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
    }
    if (class_exists('TCPDF')) {
        $pdf = new TCPDF('L', 'mm', 'A4');
        $pdf->SetCreator('Cyber Sathi');
        $pdf->SetAuthor(FOUNDER_NAME);
        $pdf->SetTitle('Cyber Awareness Champion Certificate');
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();

        $logoPath = __DIR__ . '/assets/logo.svg';
        if (file_exists($logoPath)) {
            $pdf->ImageSVG($logoPath, 20, 18, 24, 24, '', '', '', 0, false);
        }

        $html = '<div style="border:3px solid #1e3a8a;border-radius:12px;padding:18px;text-align:center;">';
        $html .= '<h1 style="color:#1e3a8a;letter-spacing:1px;">CYBER AWARENESS CHAMPION</h1>';
        $html .= '<h3 style="color:#334155;">Cyber Sathi Certificate of Achievement</h3>';
        $html .= '<p style="font-size:15px;">This certifies that</p>';
        $html .= '<h2 style="color:#0f172a;">'.e($participantName).'</h2>';
        $html .= '<p style="font-size:13px;">Email: '.e($email).' | Mobile: '.e($mobile).' | City: '.e($city).'</p>';
        $html .= '<p style="font-size:14px;">has successfully completed the Cyber Sathi Quiz with score <strong>'.(int)$r['score'].'/'.(int)$r['total_questions'].'</strong>.</p>';
        $html .= '<p style="font-size:12px;">Certificate No: '.e($certNo).' | Issued On: '.e($issuedOn).'</p>';
        $html .= '<p style="margin-top:12px;">Founder Signature: '.FOUNDER_NAME.'</p>';
        $html .= '</div>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('cyber-sathi-certificate-'.$certNo.'.pdf', 'D');
        exit;
    }
}
?><!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cyber Sathi Certificate</title>
  <style>
    body{font-family:Georgia,serif;background:linear-gradient(180deg,#eef6ff,#ffffff);padding:20px}
    .cert{margin:0 auto;border:10px solid #1d4ed8;padding:35px;max-width:1050px;text-align:center;background:#fff;box-shadow:0 12px 30px rgba(0,0,0,.15)}
    .inner{border:2px dashed #1e40af;padding:25px}
    h1{color:#1e3a8a;letter-spacing:2px;margin:8px 0}
    h2{color:#0f172a;margin:10px 0;font-size:42px}
    .meta{font-size:14px;color:#334155}
    .row{display:flex;justify-content:space-between;margin-top:25px;align-items:end}
    .btn{display:inline-block;padding:10px 16px;background:#1d4ed8;color:#fff;text-decoration:none;border-radius:6px;margin-top:14px}
  </style>
</head>
<body>
<div class="cert">
  <div class="inner">
    <img src="/assets/logo.svg" width="85" alt="Cyber Sathi logo">
    <h1>CYBER AWARENESS CHAMPION</h1>
    <p><strong>Cyber Sathi – Certificate of Achievement</strong></p>
    <p>This certifies that</p>
    <h2><?= e($participantName) ?></h2>
    <p class="meta">Email: <?= e($email) ?> | Mobile: <?= e($mobile) ?> | City: <?= e($city) ?></p>
    <p>has successfully completed Cyber Sathi Quiz with score <strong><?= (int)$r['score'] ?>/<?= (int)$r['total_questions'] ?></strong>.</p>
    <p class="meta">Certificate No: <?= e($certNo) ?> | Issued On: <?= e($issuedOn) ?></p>
    <div class="row">
      <div><strong>Founder:</strong> <?= e(FOUNDER_NAME) ?></div>
      <div><img src="/assets/sign.svg" width="180" alt="signature"><br><small>Authorized Signature</small></div>
    </div>
    <a class="btn" href="?result_id=<?= (int)$r['id'] ?>&download=1">Download PDF Certificate</a>
    <?php if(!class_exists('TCPDF')): ?><p><small>Install TCPDF using <code>composer require tecnickcom/tcpdf</code> for server-side PDF download.</small></p><?php endif; ?>
  </div>
</div>
</body>
</html>
