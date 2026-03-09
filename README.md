# Cyber Sathi – Cyber Awareness & Fraud Help Platform

Founder: **Deelip Jaiswal**  
Location: **Shajapur, Madhya Pradesh, India**

Official domain: https://cybersathi.free-education.fun/  
Book website: https://cybersathi.my.canva.site/

## Contact
- cybersathideelip@gmail.com
- jaiswaldilip8@gmail.com
- +91 8889473954

## Tech Stack
- Frontend: HTML5, Bootstrap 5, JavaScript, AJAX
- Backend: PHP + MySQL (SQLite fallback auto-bootstrap)
- Libraries: TCPDF, PHPMailer integration point, Chart-style dashboard cards, Firebase realtime chat
- Mobile: React Native starter (`mobile-app/`) + mobile JSON APIs under `api/mobile/`

## Implemented Production Modules
- User registration/login and role-based dashboards (User, Volunteer, Admin, Super Admin)
- OTP email verification during registration (10-min expiry)
- Captcha-protected login and forgot/reset password flow (30-min token expiry)
- Cyber fraud complaint system with evidence upload + admin assignment to volunteers
- Realtime live chat (Firebase + API fallback)
- Login-protected quiz with timer, scoring and professional certificate generation (PDF)
- Attractive bordered certificate with participant full details, certificate ID, logo, founder signature
- Leaderboard (top quiz performers)
- Live fraud alert system (homepage banner + scrolling ticker)
- Cyber scam tools: SMS detection, WhatsApp text guidance, fake website checker, fraud phone DB
- Email communication module with SMTP settings (single/bulk send with PHPMailer/mail fallback)
- Courses module with lessons/material links
- WordPress-style admin dashboard and full super-admin controls
- Mobile app compatible alert/leaderboard/course APIs

## Super Admin
- Default: `superadmin@cybersathi.org / super123` (change immediately)

## Setup
1. Import SQL (optional in MySQL mode; app auto-bootstraps too):
   ```bash
   mysql -u root -p < database/cybersathi.sql
   ```
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Configure env vars:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`
   - `FIREBASE_API_KEY`, `FIREBASE_AUTH_DOMAIN`, `FIREBASE_PROJECT_ID`
4. Serve with Apache/Nginx + PHP 8.1+.

## Key URLs
- `/index.php`, `/complaint.php`, `/quiz.php`, `/leaderboard.php`, `/tools.php`, `/courses.php`, `/feedback.php`, `/verify-otp.php`
- `/admin/login.php`, `/admin/index.php`
- `/api/mobile/index.php`

## Full Code ZIP Export
Generate a distributable source ZIP from repository root:
```bash
bash scripts/export_full_code_zip.sh
```
Output file: `CyberSathi-FullCode.zip`
