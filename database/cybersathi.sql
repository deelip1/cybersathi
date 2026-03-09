CREATE DATABASE IF NOT EXISTS cybersathi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cybersathi;

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  mobile VARCHAR(20) NOT NULL,
  city VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
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
  approval_status ENUM('pending','approved','rejected') DEFAULT 'pending',
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
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_chat_created_at (created_at)
);

CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_type ENUM('Seminar','Webinar','News','Media') NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  event_date DATE,
  location VARCHAR(150),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE media_gallery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  caption VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE videos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  video_url VARCHAR(255) NOT NULL,
  category VARCHAR(100) DEFAULT 'Awareness',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin(name,email,password_hash) VALUES
('Cyber Sathi Admin','admin@cybersathi.org','$2y$10$kcIhW31Wq0nBym8eJf8h2eWQJZ4J6S/O45xlX4xWQraU4/2z7Si4C');

INSERT INTO quiz_questions(question,option_1,option_2,option_3,option_4,correct_option,category) VALUES
('You receive a UPI collect request from unknown user. What should you do?','Approve quickly','Reject and report','Share OTP','Ignore bank SMS',2,'UPI Fraud'),
('Caller claims to be bank officer and asks OTP.','Share OTP for verification','Refuse and call official helpline','Send debit card photo','Install remote app',2,'OTP Scam'),
('Google task scam promises easy income.','Pay registration fee','Share Aadhaar and PAN','Avoid and report fraud','Forward to friends',3,'Google Task Scam'),
('QR code scam usually happens when','You receive money','You scan code to receive money','You scan malicious code and lose money','No risk exists',3,'QR Code Fraud');
