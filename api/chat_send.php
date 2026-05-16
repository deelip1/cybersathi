<?php
require_once __DIR__ . '/../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit; }
header('Content-Type: application/json');
$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
if(!validate_csrf_token($csrf)){ http_response_code(419); echo json_encode(['error'=>'Invalid CSRF token']); exit; }
$payload=json_decode(file_get_contents('php://input'),true);
$stmt=$pdo->prepare('INSERT INTO chat_messages(sender_name,message,sender_role) VALUES(?,?,?)');
$stmt->execute([trim($payload['sender'] ?? 'Visitor'),trim($payload['message'] ?? ''),'visitor']);
echo json_encode(['ok'=>true]);
