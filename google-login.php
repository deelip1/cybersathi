<?php
require_once __DIR__ . '/config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit('Method not allowed'); }
if(!validate_csrf_token($_POST['csrf_token'] ?? null)){ http_response_code(419); exit('Invalid CSRF token'); }
$idToken = trim((string)($_POST['credential'] ?? ''));
if($idToken==='' || GOOGLE_CLIENT_ID===''){ header('Location: /login.php?msg='.urlencode('Google login is not configured.')); exit; }

$verifyUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
$response = @file_get_contents($verifyUrl);
$data = $response ? json_decode($response, true) : null;
if(!$data || empty($data['email']) || ($data['aud'] ?? '') !== GOOGLE_CLIENT_ID){
  header('Location: /login.php?msg='.urlencode('Google authentication failed. Please try again.')); exit;
}

$email = trim((string)$data['email']);
$name = trim((string)($data['name'] ?? explode('@',$email)[0]));
$stmt = $pdo->prepare('SELECT id,name,email,city FROM users WHERE email=? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
if(!$user){
  $city='Not Provided';
  $mobile='';
  $randomPass = password_hash(bin2hex(random_bytes(12)), PASSWORD_BCRYPT);
  $ins=$pdo->prepare('INSERT INTO users(name,email,mobile,city,password_hash,login_type,is_verified) VALUES(?,?,?,?,?,"google",1)');
  $ins->execute([$name,$email,$mobile,$city,$randomPass]);
  $uid=(int)$pdo->lastInsertId();
  $user=['id'=>$uid,'name'=>$name,'email'=>$email,'city'=>$city];
} else {
  $pdo->prepare('UPDATE users SET login_type="google", is_verified=1 WHERE id=?')->execute([(int)$user['id']]);
}

$_SESSION['user_id']=(int)$user['id'];
$_SESSION['user_name']=$user['name'];
header('Location: /quiz.php?welcome=' . urlencode('Welcome to Cyber Sathi. Start the Cyber Awareness Quiz.'));
