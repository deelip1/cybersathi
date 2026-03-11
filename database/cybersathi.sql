CREATE DATABASE IF NOT EXISTS cybersathi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cybersathi;

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','super_admin') DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  mobile VARCHAR(20) NOT NULL,
  city VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  login_type ENUM('normal','google') DEFAULT 'normal',
  is_verified TINYINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE volunteers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  mobile VARCHAR(20) NOT NULL,
  city VARCHAR(100) NOT NULL,
  skills VARCHAR(255) NOT NULL,
  occupation VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  approval_status ENUM('pending','approved','rejected') DEFAULT 'pending',
  volunteer_role ENUM('Cyber Pioneer','Cyber Volunteer','Cyber Mentor','Cyber Trainer') DEFAULT 'Cyber Volunteer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE complaints (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_name VARCHAR(120) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  email VARCHAR(160) NOT NULL,
  city VARCHAR(100) NOT NULL,
  fraud_type VARCHAR(120) NOT NULL,
  description TEXT NOT NULL,
  suspected_source VARCHAR(255),
  evidence_path VARCHAR(255),
  status ENUM('open','in_progress','closed') DEFAULT 'open',
  assigned_to INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (assigned_to) REFERENCES volunteers(id) ON DELETE SET NULL
);
CREATE TABLE quiz_questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question TEXT NOT NULL,
  option_1 VARCHAR(255) NOT NULL,
  option_2 VARCHAR(255) NOT NULL,
  option_3 VARCHAR(255) NOT NULL,
  option_4 VARCHAR(255) NOT NULL,
  correct_option TINYINT NOT NULL,
  category VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE quiz_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  participant_name VARCHAR(120) NOT NULL,
  participant_email VARCHAR(180),
  participant_city VARCHAR(120),
  score INT NOT NULL,
  total_questions INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE TABLE chat_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  complaint_id INT NULL,
  sender_role ENUM('visitor','volunteer','admin') NOT NULL,
  sender_name VARCHAR(120) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_type ENUM('Seminar','Webinar','News','Media') NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  event_date DATE,
  location VARCHAR(150),
  thumbnail VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE videos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  video_url VARCHAR(255) NOT NULL,
  thumbnail VARCHAR(255),
  category VARCHAR(100) DEFAULT 'Awareness',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE fraud_alerts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  message TEXT NOT NULL,
  severity ENUM('low','medium','high') DEFAULT 'medium',
  is_active TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE fraud_phone_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  phone VARCHAR(20) NOT NULL,
  fraud_type VARCHAR(120) NOT NULL,
  report_count INT DEFAULT 1,
  is_verified TINYINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  video_url VARCHAR(255),
  pdf_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE email_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject VARCHAR(255) NOT NULL,
  recipient_type VARCHAR(50) NOT NULL,
  recipients_count INT DEFAULT 0,
  status VARCHAR(50) DEFAULT 'queued',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE email_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  smtp_host VARCHAR(180),
  smtp_port INT DEFAULT 587,
  smtp_username VARCHAR(180),
  smtp_password VARCHAR(255),
  encryption VARCHAR(10) DEFAULT 'tls',
  sender_email VARCHAR(180),
  sender_name VARCHAR(180),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE otp_verification (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(180),
  otp_code VARCHAR(10),
  expires_at DATETIME,
  is_used TINYINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE password_reset_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(180),
  token VARCHAR(80),
  expires_at DATETIME,
  is_used TINYINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120),
  email VARCHAR(180),
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE fraud_patterns (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pattern_type VARCHAR(80),
  pattern_value VARCHAR(255),
  frequency INT DEFAULT 1,
  last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE fraud_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  state_name VARCHAR(120),
  city_name VARCHAR(120),
  fraud_type VARCHAR(120),
  count_reports INT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin(name,email,password_hash,role) VALUES
('Cyber Sathi Admin','admin@cybersathi.org','$2y$10$kcIhW31Wq0nBym8eJf8h2eWQJZ4J6S/O45xlX4xWQraU4/2z7Si4C','admin'),
('Super Admin','superadmin@cybersathi.org','$2y$12$bmj14mqCug2M9DY386z9CeF.6EVZ.EB279OakaSB/FuLObRPMRC8O','super_admin');

INSERT INTO fraud_alerts(title,message,severity,is_active) VALUES
('Urgent Fraud Alert','Warning: Google Task Scam is increasing. Do not pay registration fees.','high',1);
