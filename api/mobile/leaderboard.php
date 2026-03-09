<?php require_once __DIR__ . '/../../config/db.php'; header('Content-Type: application/json');
$q='SELECT qr.participant_name,u.city,MAX(qr.score) as score FROM quiz_results qr LEFT JOIN users u ON u.id=qr.user_id GROUP BY qr.participant_name,u.city ORDER BY score DESC LIMIT 50';
echo json_encode($pdo->query($q)->fetchAll());
