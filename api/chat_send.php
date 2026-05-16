<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
if (!validate_csrf_token($csrf)) {
    http_response_code(419);
    echo json_encode(['ok' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput ?: '{}', true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON payload']);
    exit;
}

$sender = trim((string)($payload['sender'] ?? 'Visitor'));
$message = trim((string)($payload['message'] ?? ''));

if ($sender === '') {
    $sender = 'Visitor';
}
if (mb_strlen($sender) > 120) {
    $sender = mb_substr($sender, 0, 120);
}

if ($message === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Message is required']);
    exit;
}
if (mb_strlen($message) > 2000) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Message exceeds 2000 characters']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO chat_messages(sender_name, message, sender_role) VALUES(?, ?, ?)');
$stmt->execute([$sender, $message, 'visitor']);

echo json_encode(['ok' => true]);
