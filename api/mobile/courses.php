<?php require_once __DIR__ . '/../../config/db.php'; header('Content-Type: application/json');
echo json_encode($pdo->query('SELECT id,title,description,video_url,pdf_url FROM courses ORDER BY id DESC')->fetchAll());
