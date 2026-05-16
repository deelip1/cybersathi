<?php require_once __DIR__ . '/../../config/db.php'; header('Content-Type: application/json');
echo json_encode($pdo->query('SELECT id,title,message,severity,created_at FROM fraud_alerts WHERE is_active=1 ORDER BY id DESC LIMIT 20')->fetchAll());
