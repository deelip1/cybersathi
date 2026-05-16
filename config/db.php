<?php
require_once __DIR__ . '/config.php';
$host=getenv('DB_HOST')?:'127.0.0.1'; $db=getenv('DB_NAME')?:'cybersathi'; $user=getenv('DB_USER')?:'root'; $pass=getenv('DB_PASS')?:'';
$mysqlDsn="mysql:host=$host;dbname=$db;charset=utf8mb4";
$options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];

function base_seed(PDO $pdo): void {
  if((int)$pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn()===0){
    $pdo->prepare('INSERT INTO admin(name,email,password_hash,role) VALUES(?,?,?,?)')->execute(['Cyber Sathi Admin','admin@cybersathi.org',password_hash('admin123',PASSWORD_BCRYPT),'admin']);
  }
  $s=$pdo->prepare('SELECT COUNT(*) FROM admin WHERE email=?');$s->execute([SUPER_ADMIN_EMAIL]);
  if((int)$s->fetchColumn()===0){$pdo->prepare('INSERT INTO admin(name,email,password_hash,role) VALUES(?,?,?,?)')->execute(['Super Admin',SUPER_ADMIN_EMAIL,password_hash(SUPER_ADMIN_DEFAULT_PASSWORD,PASSWORD_BCRYPT),'super_admin']);}
  if((int)$pdo->query('SELECT COUNT(*) FROM quiz_questions')->fetchColumn()===0){
    $pdo->exec("INSERT INTO quiz_questions(question,option_1,option_2,option_3,option_4,correct_option,category) VALUES
    ('You receive a UPI collect request from unknown user. What should you do?','Approve quickly','Reject and report','Share OTP','Ignore bank SMS',2,'UPI Fraud'),
    ('Caller claims to be bank officer and asks OTP.','Share OTP for verification','Refuse and call official helpline','Send debit card photo','Install remote app',2,'OTP Scam')");
$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'cybersathi';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$charset = 'utf8mb4';
$mysqlDsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];

function seed_admins(PDO $pdo): void {
  $count=(int)$pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn();
  if($count===0){$pdo->prepare('INSERT INTO admin(name,email,password_hash,role) VALUES(?,?,?,?)')->execute(['Cyber Sathi Admin','admin@cybersathi.org',password_hash('admin123', PASSWORD_BCRYPT),'admin']);}
  $check=$pdo->prepare('SELECT COUNT(*) FROM admin WHERE email=?');$check->execute([SUPER_ADMIN_EMAIL]);
  if((int)$check->fetchColumn()===0){$pdo->prepare('INSERT INTO admin(name,email,password_hash,role) VALUES(?,?,?,?)')->execute(['Super Admin',SUPER_ADMIN_EMAIL,password_hash(SUPER_ADMIN_DEFAULT_PASSWORD,PASSWORD_BCRYPT),'super_admin']);}
}
function seed_quiz(PDO $pdo): void {
  if((int)$pdo->query('SELECT COUNT(*) FROM quiz_questions')->fetchColumn()===0){
    $pdo->exec("INSERT INTO quiz_questions(question,option_1,option_2,option_3,option_4,correct_option,category) VALUES
    ('You receive a UPI collect request from unknown user. What should you do?','Approve quickly','Reject and report','Share OTP','Ignore bank SMS',2,'UPI Fraud'),
    ('Caller claims to be bank officer and asks OTP.','Share OTP for verification','Refuse and call official helpline','Send debit card photo','Install remote app',2,'OTP Scam'),
    ('Google task scam promises easy income.','Pay registration fee','Share Aadhaar and PAN','Avoid and report fraud','Forward to friends',3,'Task Scam')");
  }
}

function bootstrap_mysql(PDO $pdo): void {
  $pdo->exec("CREATE TABLE IF NOT EXISTS admin(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120),email VARCHAR(160) UNIQUE,password_hash VARCHAR(255),role ENUM('admin','super_admin') DEFAULT 'admin',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120),email VARCHAR(160) UNIQUE,mobile VARCHAR(20),city VARCHAR(100),password_hash VARCHAR(255),login_type ENUM('normal','google') DEFAULT 'normal',is_verified TINYINT DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS volunteers(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120),email VARCHAR(160) UNIQUE,mobile VARCHAR(20),city VARCHAR(100),skills VARCHAR(255),occupation VARCHAR(120),password_hash VARCHAR(255),approval_status ENUM('pending','approved','rejected') DEFAULT 'pending',volunteer_role ENUM('Cyber Pioneer','Cyber Volunteer','Cyber Mentor','Cyber Trainer') DEFAULT 'Cyber Volunteer',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS complaints(id INT AUTO_INCREMENT PRIMARY KEY,user_name VARCHAR(120),mobile VARCHAR(20),email VARCHAR(160),city VARCHAR(100),fraud_type VARCHAR(120),description TEXT,suspected_source VARCHAR(255),evidence_path VARCHAR(255),status ENUM('open','in_progress','closed') DEFAULT 'open',assigned_to INT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY (assigned_to) REFERENCES volunteers(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_questions(id INT AUTO_INCREMENT PRIMARY KEY,question TEXT,option_1 VARCHAR(255),option_2 VARCHAR(255),option_3 VARCHAR(255),option_4 VARCHAR(255),correct_option TINYINT,category VARCHAR(100),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_results(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,participant_name VARCHAR(120),participant_email VARCHAR(180),participant_city VARCHAR(120),score INT,total_questions INT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS chat_messages(id INT AUTO_INCREMENT PRIMARY KEY,complaint_id INT NULL,sender_role ENUM('visitor','volunteer','admin') NOT NULL,sender_name VARCHAR(120),message TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS events(id INT AUTO_INCREMENT PRIMARY KEY,event_type ENUM('Seminar','Webinar','News','Media') NOT NULL,title VARCHAR(180),description TEXT,event_date DATE,location VARCHAR(150),thumbnail VARCHAR(255),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS videos(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(180),video_url VARCHAR(255),thumbnail VARCHAR(255),category VARCHAR(100) DEFAULT 'Awareness',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120),email VARCHAR(160) UNIQUE,mobile VARCHAR(20),city VARCHAR(100),password_hash VARCHAR(255),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS volunteers(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120),email VARCHAR(160) UNIQUE,mobile VARCHAR(20),city VARCHAR(100),skills VARCHAR(255),occupation VARCHAR(120),password_hash VARCHAR(255),approval_status ENUM('pending','approved','rejected') DEFAULT 'pending',volunteer_role ENUM('Cyber Pioneer','Cyber Volunteer','Cyber Mentor','Cyber Trainer') DEFAULT 'Cyber Volunteer',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS complaints(id INT AUTO_INCREMENT PRIMARY KEY,user_name VARCHAR(120),mobile VARCHAR(20),email VARCHAR(160),city VARCHAR(100),fraud_type VARCHAR(120),description TEXT,suspected_source VARCHAR(255),evidence_path VARCHAR(255),status ENUM('open','in_progress','closed') DEFAULT 'open',assigned_to INT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY (assigned_to) REFERENCES volunteers(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_questions(id INT AUTO_INCREMENT PRIMARY KEY,question TEXT,option_1 VARCHAR(255),option_2 VARCHAR(255),option_3 VARCHAR(255),option_4 VARCHAR(255),correct_option TINYINT,category VARCHAR(100),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_results(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NULL,participant_name VARCHAR(120),score INT,total_questions INT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS chat_messages(id INT AUTO_INCREMENT PRIMARY KEY,complaint_id INT NULL,sender_role ENUM('visitor','volunteer','admin') NOT NULL,sender_name VARCHAR(120),message TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX idx_chat_created_at (created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS events(id INT AUTO_INCREMENT PRIMARY KEY,event_type ENUM('Seminar','Webinar','News','Media') NOT NULL,title VARCHAR(180),description TEXT,event_date DATE,location VARCHAR(150),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS videos(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(180),video_url VARCHAR(255),category VARCHAR(100) DEFAULT 'Awareness',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS fraud_alerts(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(180),message TEXT,severity ENUM('low','medium','high') DEFAULT 'medium',is_active TINYINT DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS fraud_phone_reports(id INT AUTO_INCREMENT PRIMARY KEY,phone VARCHAR(20),fraud_type VARCHAR(120),report_count INT DEFAULT 1,is_verified TINYINT DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS courses(id INT AUTO_INCREMENT PRIMARY KEY,title VARCHAR(180),description TEXT,video_url VARCHAR(255),pdf_url VARCHAR(255),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS email_logs(id INT AUTO_INCREMENT PRIMARY KEY,subject VARCHAR(255),recipient_type VARCHAR(50),recipients_count INT DEFAULT 0,status VARCHAR(50),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS email_settings(id INT AUTO_INCREMENT PRIMARY KEY,smtp_host VARCHAR(180),smtp_port INT DEFAULT 587,smtp_username VARCHAR(180),smtp_password VARCHAR(255),encryption VARCHAR(10) DEFAULT 'tls',sender_email VARCHAR(180),sender_name VARCHAR(180),updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS otp_verification(id INT AUTO_INCREMENT PRIMARY KEY,email VARCHAR(180),otp_code VARCHAR(10),expires_at DATETIME,is_used TINYINT DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens(id INT AUTO_INCREMENT PRIMARY KEY,email VARCHAR(180),token VARCHAR(80),expires_at DATETIME,is_used TINYINT DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS feedback(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120),email VARCHAR(180),message TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS fraud_patterns(id INT AUTO_INCREMENT PRIMARY KEY,pattern_type VARCHAR(80),pattern_value VARCHAR(255),frequency INT DEFAULT 1,last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS fraud_reports(id INT AUTO_INCREMENT PRIMARY KEY,state_name VARCHAR(120),city_name VARCHAR(120),fraud_type VARCHAR(120),count_reports INT DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  try{$pdo->exec('ALTER TABLE users ADD COLUMN is_verified TINYINT DEFAULT 0');}catch(Throwable $e){}
  try{$pdo->exec("ALTER TABLE users ADD COLUMN login_type ENUM('normal','google') DEFAULT 'normal'");}catch(Throwable $e){}
  try{$pdo->exec('ALTER TABLE events ADD COLUMN thumbnail VARCHAR(255)');}catch(Throwable $e){}
  try{$pdo->exec('ALTER TABLE videos ADD COLUMN thumbnail VARCHAR(255)');}catch(Throwable $e){}
  try{$pdo->exec('ALTER TABLE quiz_results ADD COLUMN participant_email VARCHAR(180) NULL');}catch(Throwable $e){}
  try{$pdo->exec('ALTER TABLE quiz_results ADD COLUMN participant_city VARCHAR(120) NULL');}catch(Throwable $e){}
  base_seed($pdo);
  try{$pdo->exec('ALTER TABLE admin ADD COLUMN role ENUM("admin","super_admin") DEFAULT "admin"');}catch(Throwable $e){}
  try{$pdo->exec('ALTER TABLE volunteers ADD COLUMN volunteer_role ENUM("Cyber Pioneer","Cyber Volunteer","Cyber Mentor","Cyber Trainer") DEFAULT "Cyber Volunteer"');}catch(Throwable $e){}
  seed_admins($pdo);seed_quiz($pdo);
  if((int)$pdo->query('SELECT COUNT(*) FROM fraud_alerts')->fetchColumn()===0){$pdo->exec("INSERT INTO fraud_alerts(title,message,severity,is_active) VALUES ('Urgent Fraud Alert','Warning: Google Task Scam is increasing. Do not pay registration fees.','high',1)");}
  if((int)$pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn()===0){$pdo->exec("INSERT INTO courses(title,description,video_url,pdf_url) VALUES ('UPI Fraud Awareness','Learn secure UPI usage and scam prevention.','#','#'),('Social Media Security','Protect your account against impersonation and phishing.','#','#')");}
}

function bootstrap_sqlite(PDO $pdo): void {
  $pdo->exec("CREATE TABLE IF NOT EXISTS admin(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT,email TEXT UNIQUE,password_hash TEXT,role TEXT DEFAULT 'admin',created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT,email TEXT UNIQUE,mobile TEXT,city TEXT,password_hash TEXT,login_type TEXT DEFAULT 'normal',is_verified INTEGER DEFAULT 0,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS volunteers(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT,email TEXT UNIQUE,mobile TEXT,city TEXT,skills TEXT,occupation TEXT,password_hash TEXT,approval_status TEXT DEFAULT 'pending',volunteer_role TEXT DEFAULT 'Cyber Volunteer',created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS complaints(id INTEGER PRIMARY KEY AUTOINCREMENT,user_name TEXT,mobile TEXT,email TEXT,city TEXT,fraud_type TEXT,description TEXT,suspected_source TEXT,evidence_path TEXT,status TEXT DEFAULT 'open',assigned_to INTEGER,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_questions(id INTEGER PRIMARY KEY AUTOINCREMENT,question TEXT,option_1 TEXT,option_2 TEXT,option_3 TEXT,option_4 TEXT,correct_option INTEGER,category TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_results(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER,participant_name TEXT,participant_email TEXT,participant_city TEXT,score INTEGER,total_questions INTEGER,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS chat_messages(id INTEGER PRIMARY KEY AUTOINCREMENT,complaint_id INTEGER,sender_role TEXT,sender_name TEXT,message TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS events(id INTEGER PRIMARY KEY AUTOINCREMENT,event_type TEXT,title TEXT,description TEXT,event_date TEXT,location TEXT,thumbnail TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS videos(id INTEGER PRIMARY KEY AUTOINCREMENT,title TEXT,video_url TEXT,thumbnail TEXT,category TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT,email TEXT UNIQUE,mobile TEXT,city TEXT,password_hash TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS volunteers(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT,email TEXT UNIQUE,mobile TEXT,city TEXT,skills TEXT,occupation TEXT,password_hash TEXT,approval_status TEXT DEFAULT 'pending',volunteer_role TEXT DEFAULT 'Cyber Volunteer',created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS complaints(id INTEGER PRIMARY KEY AUTOINCREMENT,user_name TEXT,mobile TEXT,email TEXT,city TEXT,fraud_type TEXT,description TEXT,suspected_source TEXT,evidence_path TEXT,status TEXT DEFAULT 'open',assigned_to INTEGER,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_questions(id INTEGER PRIMARY KEY AUTOINCREMENT,question TEXT,option_1 TEXT,option_2 TEXT,option_3 TEXT,option_4 TEXT,correct_option INTEGER,category TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_results(id INTEGER PRIMARY KEY AUTOINCREMENT,user_id INTEGER,participant_name TEXT,score INTEGER,total_questions INTEGER,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS chat_messages(id INTEGER PRIMARY KEY AUTOINCREMENT,complaint_id INTEGER,sender_role TEXT,sender_name TEXT,message TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS events(id INTEGER PRIMARY KEY AUTOINCREMENT,event_type TEXT,title TEXT,description TEXT,event_date TEXT,location TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS videos(id INTEGER PRIMARY KEY AUTOINCREMENT,title TEXT,video_url TEXT,category TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS fraud_alerts(id INTEGER PRIMARY KEY AUTOINCREMENT,title TEXT,message TEXT,severity TEXT,is_active INTEGER DEFAULT 1,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS fraud_phone_reports(id INTEGER PRIMARY KEY AUTOINCREMENT,phone TEXT,fraud_type TEXT,report_count INTEGER DEFAULT 1,is_verified INTEGER DEFAULT 0,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS courses(id INTEGER PRIMARY KEY AUTOINCREMENT,title TEXT,description TEXT,video_url TEXT,pdf_url TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS email_logs(id INTEGER PRIMARY KEY AUTOINCREMENT,subject TEXT,recipient_type TEXT,recipients_count INTEGER DEFAULT 0,status TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS email_settings(id INTEGER PRIMARY KEY AUTOINCREMENT,smtp_host TEXT,smtp_port INTEGER DEFAULT 587,smtp_username TEXT,smtp_password TEXT,encryption TEXT DEFAULT 'tls',sender_email TEXT,sender_name TEXT,updated_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS otp_verification(id INTEGER PRIMARY KEY AUTOINCREMENT,email TEXT,otp_code TEXT,expires_at TEXT,is_used INTEGER DEFAULT 0,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens(id INTEGER PRIMARY KEY AUTOINCREMENT,email TEXT,token TEXT,expires_at TEXT,is_used INTEGER DEFAULT 0,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS feedback(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT,email TEXT,message TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS fraud_patterns(id INTEGER PRIMARY KEY AUTOINCREMENT,pattern_type TEXT,pattern_value TEXT,frequency INTEGER DEFAULT 1,last_seen TEXT DEFAULT CURRENT_TIMESTAMP)");
  $pdo->exec("CREATE TABLE IF NOT EXISTS fraud_reports(id INTEGER PRIMARY KEY AUTOINCREMENT,state_name TEXT,city_name TEXT,fraud_type TEXT,count_reports INTEGER DEFAULT 1,created_at TEXT DEFAULT CURRENT_TIMESTAMP)");
  try{$pdo->exec('ALTER TABLE users ADD COLUMN is_verified INTEGER DEFAULT 0');}catch(Throwable $e){}
  try{$pdo->exec("ALTER TABLE users ADD COLUMN login_type TEXT DEFAULT 'normal'");}catch(Throwable $e){}
  try{$pdo->exec('ALTER TABLE events ADD COLUMN thumbnail TEXT');}catch(Throwable $e){}
  try{$pdo->exec('ALTER TABLE videos ADD COLUMN thumbnail TEXT');}catch(Throwable $e){}
  try{$pdo->exec('ALTER TABLE quiz_results ADD COLUMN participant_email TEXT');}catch(Throwable $e){}
  try{$pdo->exec('ALTER TABLE quiz_results ADD COLUMN participant_city TEXT');}catch(Throwable $e){}
  base_seed($pdo);
}

function get_email_settings(PDO $pdo): array {
  try{$r=$pdo->query('SELECT * FROM email_settings ORDER BY id DESC LIMIT 1')->fetch(); return $r?:[];}catch(Throwable $e){return [];}
  seed_admins($pdo);seed_quiz($pdo);
  if((int)$pdo->query('SELECT COUNT(*) FROM fraud_alerts')->fetchColumn()===0){$pdo->exec("INSERT INTO fraud_alerts(title,message,severity,is_active) VALUES ('Urgent Fraud Alert','Warning: Google Task Scam is increasing. Do not pay registration fees.','high',1)");}
  if((int)$pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn()===0){$pdo->exec("INSERT INTO courses(title,description,video_url,pdf_url) VALUES ('UPI Fraud Awareness','Learn secure UPI usage and scam prevention.','#','#'),('Social Media Security','Protect your account against impersonation and phishing.','#','#')");}
}

try{$pdo=new PDO($mysqlDsn,$user,$pass,$options);$dbDriver='mysql';bootstrap_mysql($pdo);}catch(PDOException $e){$pdo=new PDO('sqlite:' . __DIR__ . '/../database/cybersathi.sqlite');$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);$dbDriver='sqlite';bootstrap_sqlite($pdo);} 
