<?php
require_once __DIR__ . '/../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit; }
if(!validate_csrf_token($_POST['csrf_token'] ?? null)){ http_response_code(419); exit('Invalid CSRF token'); }
if(!verify_recaptcha($_POST['g-recaptcha-response'] ?? null)){ http_response_code(400); exit('reCAPTCHA verification failed'); }
$stmt=$pdo->prepare('INSERT INTO volunteers(name,email,mobile,city,skills,occupation,password_hash,approval_status) VALUES(?,?,?,?,?,?,?,?)');
$stmt->execute([
  trim($_POST['name']),trim($_POST['email']),trim($_POST['mobile']),trim($_POST['city']),
  trim($_POST['skills']),trim($_POST['occupation']),password_hash($_POST['password'], PASSWORD_BCRYPT),'pending'
]);
header('Location: /volunteer.php?success=1');
