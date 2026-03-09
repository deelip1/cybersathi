<?php
require_once __DIR__ . '/../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit; }
$captcha = $_POST['captcha'] ?? 'ok'; // placeholder hook
if($captcha===''){ die('Captcha validation failed'); }
$uploadPath = null;
if(!empty($_FILES['evidence']['name'])){
  $allowed = ['image/jpeg','image/png','application/pdf'];
  if(!in_array($_FILES['evidence']['type'],$allowed,true)) die('Invalid file type');
  if($_FILES['evidence']['size'] > 5*1024*1024) die('File too large');
  $fname = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','_',$_FILES['evidence']['name']);
  $target = __DIR__ . '/../uploads/' . $fname;
  move_uploaded_file($_FILES['evidence']['tmp_name'],$target);
  $uploadPath = 'uploads/'.$fname;
}
$stmt=$pdo->prepare('INSERT INTO complaints(user_name,mobile,email,city,fraud_type,description,suspected_source,evidence_path,status) VALUES(?,?,?,?,?,?,?,?,?)');
$stmt->execute([trim($_POST['name']),trim($_POST['mobile']),trim($_POST['email']),trim($_POST['city']),trim($_POST['fraud_type']),trim($_POST['description']),trim($_POST['suspected_source'] ?? ''),$uploadPath,'open']);
header('Location: /complaint.php?success=1');
