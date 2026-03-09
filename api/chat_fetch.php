<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');
$rows=$pdo->query('SELECT sender_name,message,created_at FROM chat_messages ORDER BY id DESC LIMIT 30')->fetchAll();
echo json_encode(array_reverse($rows));
