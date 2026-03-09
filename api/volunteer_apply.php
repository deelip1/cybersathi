<?php
require_once __DIR__ . '/../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit; }
$stmt=$pdo->prepare('INSERT INTO volunteers(name,email,mobile,city,skills,occupation,approval_status) VALUES(?,?,?,?,?,?,?)');
$stmt->execute([trim($_POST['name']),trim($_POST['email']),trim($_POST['mobile']),trim($_POST['city']),trim($_POST['skills']),trim($_POST['occupation']),'pending']);
header('Location: /volunteer.php?success=1');
