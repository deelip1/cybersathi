<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$stmt = $pdo->query('SELECT sender_name, message, created_at FROM chat_messages ORDER BY id DESC LIMIT 30');
$rows = $stmt->fetchAll();

echo json_encode(array_reverse($rows));
