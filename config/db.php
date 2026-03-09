<?php
require_once __DIR__ . '/config.php';
$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'cybersathi';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$charset = 'utf8mb4';
$mysqlDsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

function bootstrap_sqlite(PDO $pdo): void {
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, mobile TEXT, city TEXT, password_hash TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS volunteers (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, mobile TEXT, city TEXT, skills TEXT, occupation TEXT, password_hash TEXT, approval_status TEXT DEFAULT "pending", created_at TEXT DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS complaints (id INTEGER PRIMARY KEY AUTOINCREMENT, user_name TEXT, mobile TEXT, email TEXT, city TEXT, fraud_type TEXT, description TEXT, suspected_source TEXT, evidence_path TEXT, status TEXT DEFAULT "open", assigned_to INTEGER, created_at TEXT DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS quiz_questions (id INTEGER PRIMARY KEY AUTOINCREMENT, question TEXT, option_1 TEXT, option_2 TEXT, option_3 TEXT, option_4 TEXT, correct_option INTEGER, category TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS quiz_results (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, participant_name TEXT, score INTEGER, total_questions INTEGER, created_at TEXT DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS chat_messages (id INTEGER PRIMARY KEY AUTOINCREMENT, complaint_id INTEGER, sender_role TEXT, sender_name TEXT, message TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS events (id INTEGER PRIMARY KEY AUTOINCREMENT, event_type TEXT, title TEXT, description TEXT, event_date TEXT, location TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS media_gallery (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, image_path TEXT, caption TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS videos (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, video_url TEXT, category TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS admin (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, password_hash TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP)');

    try { $pdo->exec('ALTER TABLE volunteers ADD COLUMN password_hash TEXT'); } catch (Throwable $e) {}

    $c=(int)$pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn();
    if($c===0){$pdo->prepare('INSERT INTO admin(name,email,password_hash) VALUES(?,?,?)')->execute(['Cyber Sathi Admin','admin@cybersathi.org',password_hash('admin123', PASSWORD_BCRYPT)]);}    
    $q=(int)$pdo->query('SELECT COUNT(*) FROM quiz_questions')->fetchColumn();
    if($q===0){
      $pdo->exec("INSERT INTO quiz_questions(question,option_1,option_2,option_3,option_4,correct_option,category) VALUES
      ('You receive a UPI collect request from unknown user. What should you do?','Approve quickly','Reject and report','Share OTP','Ignore bank SMS',2,'UPI Fraud'),
      ('Caller claims to be bank officer and asks OTP.','Share OTP for verification','Refuse and call official helpline','Send debit card photo','Install remote app',2,'OTP Scam')");
    }
}

try {
    $pdo = new PDO($mysqlDsn, $user, $pass, $options);
    $dbDriver = 'mysql';
} catch (PDOException $e) {
    $pdo = new PDO('sqlite:' . __DIR__ . '/../database/cybersathi.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbDriver = 'sqlite';
    bootstrap_sqlite($pdo);
}
