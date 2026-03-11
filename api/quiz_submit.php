<?php
require_once __DIR__ . '/../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit; }
if(!validate_csrf_token($_POST['csrf_token'] ?? null)){ http_response_code(419); exit('Invalid CSRF token'); }
if(!is_user_logged_in()){ header('Location: /login.php?msg='.urlencode('Please register or login to participate in Cyber Awareness Quiz.')); exit; }
$answers = $_POST['q'] ?? [];
if(!$answers){ die('No answers found'); }
$score=0; $total=count($answers);
$stmt=$pdo->prepare('SELECT correct_option FROM quiz_questions WHERE id=?');
foreach($answers as $qid=>$ans){ $stmt->execute([(int)$qid]); $c=(int)$stmt->fetchColumn(); if((int)$ans === $c){ $score++; } }

$participantName = trim((string)($_POST['participant_name'] ?? ($_SESSION['user_name'] ?? 'Participant')));
$participantEmail = trim((string)($_POST['participant_email'] ?? ''));
$participantCity = trim((string)($_POST['participant_city'] ?? ''));
if($participantEmail==='' || $participantCity===''){
  $u=$pdo->prepare('SELECT email,city FROM users WHERE id=?');
  $u->execute([(int)$_SESSION['user_id']]);
  $row=$u->fetch() ?: [];
  $participantEmail = $participantEmail ?: (string)($row['email'] ?? '');
  $participantCity = $participantCity ?: (string)($row['city'] ?? '');
}

$save=$pdo->prepare('INSERT INTO quiz_results(user_id,participant_name,participant_email,participant_city,score,total_questions) VALUES(?,?,?,?,?,?)');
$save->execute([(int)$_SESSION['user_id'], $participantName, $participantEmail, $participantCity, $score, $total]);
$id=(int)$pdo->lastInsertId();
header('Location: /certificate.php?result_id='.$id);
