<?php
header('Content-Type: application/json');
echo json_encode(['service'=>'Cyber Sathi Mobile API','endpoints'=>['/api/mobile/alerts.php','/api/mobile/leaderboard.php','/api/mobile/courses.php']]);
