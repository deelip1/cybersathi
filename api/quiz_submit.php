<?php
require_once __DIR__ . '/../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit; }
$answers = $_POST['q'] ?? [];
if(!$answers){ die('No answers found'); }
$score=0; $total=count($answers);
$stmt=$pdo->prepare('SELECT correct_option FROM quiz_questions WHERE id=?');
foreach($answers as $qid=>$ans){ $stmt->execute([(int)$qid]); $c=(int)$stmt->fetchColumn(); if((int)$ans === $c){ $score++; } }
$save=$pdo->prepare('INSERT INTO quiz_results(user_id,participant_name,score,total_questions) VALUES(?,?,?,?)');
$save->execute([$_SESSION['user_id'] ?? null, trim($_POST['participant_name'] ?? 'Guest'), $score, $total]);
$id=(int)$pdo->lastInsertId();
header('Location: /certificate.php?result_id='.$id);
