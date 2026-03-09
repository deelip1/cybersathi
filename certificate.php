<?php
require_once __DIR__ . '/config/db.php';
$id=(int)($_GET['result_id'] ?? 0);
$stmt=$pdo->prepare('SELECT * FROM quiz_results WHERE id=?');
$stmt->execute([$id]);
$r=$stmt->fetch();
if(!$r){ http_response_code(404); echo 'Result not found'; exit; }

$download = isset($_GET['download']);
if ($download) {
    $autoload = __DIR__ . '/vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
    }
    if (class_exists('TCPDF')) {
        $pdf = new TCPDF();
        $pdf->SetCreator('Cyber Sathi');
        $pdf->SetAuthor(FOUNDER_NAME);
        $pdf->SetTitle('Cyber Awareness Champion Certificate');
        $pdf->AddPage();
        $html = '<h1 style="color:#1e3a8a;text-align:center;">Cyber Awareness Champion</h1>';
        $html .= '<p style="text-align:center;">This certifies that</p>';
        $html .= '<h2 style="text-align:center;">'.e($r['participant_name']).'</h2>';
        $html .= '<p style="text-align:center;">has successfully completed Cyber Sathi Quiz with score <strong>'.(int)$r['score'].'/'.(int)$r['total_questions'].'</strong>.</p>';
        $html .= '<p style="text-align:center;">Founder: '.FOUNDER_NAME.'</p>';
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('cyber-sathi-certificate.pdf', 'D');
        exit;
    }
}
?><!doctype html><html><head><meta charset="utf-8"><title>Certificate</title><style>body{font-family:Arial;background:#f7fbff}.c{margin:30px auto;border:8px solid #1d4ed8;padding:40px;max-width:900px;text-align:center;background:#fff}h1{color:#1e3a8a}</style></head><body>
<div class="c">
<img src="/assets/logo.svg" width="80" alt="logo">
<h1>Cyber Awareness Champion</h1>
<p>This certifies that</p>
<h2><?= e($r['participant_name']) ?></h2>
<p>has successfully completed Cyber Sathi Quiz with score <strong><?= (int)$r['score'] ?>/<?= (int)$r['total_questions'] ?></strong>.</p>
<p>Founder: Deelip Jaiswal</p>
<img src="/assets/sign.svg" width="200" alt="sign"><br>
<a href="?result_id=<?= (int)$r['id'] ?>&download=1">Download PDF (TCPDF)</a> | <button onclick="window.print()">Print</button>
<?php if(!class_exists('TCPDF')): ?><p><small>Install TCPDF with <code>composer require tecnickcom/tcpdf</code> to enable PDF download.</small></p><?php endif; ?>
</div>
</body></html>
