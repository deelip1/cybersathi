# Cyber Sathi – Cyber Awareness & Fraud Help Platform

Founder: **Deelip Jaiswal**  
Location: **Shajapur, Madhya Pradesh, India**

Official domain: https://cybersathi.free-education.fun/  
Additional website: https://cybersathi.my.canva.site/

## Official Contact
- cybersathideelip@gmail.com
- jaiswaldilip8@gmail.com
- +91 8889473954

## Production-Ready Architecture
- **Frontend:** PHP + Bootstrap 5 + JavaScript (AJAX)
- **Backend:** Core PHP (PDO prepared statements)
- **Database:** MySQL (`database/cybersathi.sql`)
- **Security:** prepared statements, password hashing, upload validation hooks, session-based auth
- **Chat:** polling-based real-time support with extensible API
- **Certificate:** printable certificate page (TCPDF-ready integration point)
- **Mobile App:** React Native starter under `mobile-app/`

## Core Features Implemented
1. Home page with CTA buttons and live counters
2. Founder/Campaign About page
3. Cyber complaint registration with evidence upload
4. Volunteer registration and admin approval
5. Live chat interface + message API
6. AI guidance page for fraud-response steps
7. Quiz engine with random questions + timer + scoring
8. Certificate page (print/download PDF via browser)
9. Media/events pages (seminars/webinars/news/videos)
10. Admin dashboard for approvals, assignments, analytics, and event publishing

## Folder Structure
```
cyber-sathi/
  index.php
  about.php
  contact.php
  register.php
  login.php
  complaint.php
  volunteer.php
  quiz.php
  certificate.php
  live-chat.php
  ai-guidance.php
  media.php
  admin/
  api/
  config/
  includes/
  assets/css
  assets/js
  assets/images
  uploads/
  database/cybersathi.sql
  mobile-app/
```

## Installation
1. Create MySQL DB and import schema:
   ```bash
   mysql -u root -p < database/cybersathi.sql
   ```
2. Configure DB credentials in `config/db.php` or env vars (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
3. Serve using Apache/Nginx + PHP 8.1+.
4. Open `/admin/login.php`.

## Default Admin
- Email: `admin@cybersathi.org`
- Password hash seeded in SQL (change immediately in production)

## Next hardening checklist
- Replace placeholder captcha with Google reCAPTCHA
- Add CSRF tokens on all forms
- Integrate TCPDF in `certificate.php` for server-side PDF generation
- Integrate WebSocket/Firebase for true realtime chat
- Add role-based dashboards for volunteers and users

## Repository policy
- Binary runtime DB files are not committed.
- `database/cybersathi.sqlite` is generated locally at runtime when MySQL is unavailable.
- `.gitignore` excludes SQLite artifacts and upload files.
