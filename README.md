# Cyber Sathi – Cyber Awareness & Fraud Help Platform

Founder: **Deelip Jaiswal**  
Location: **Shajapur, Madhya Pradesh, India**

Official domain: https://cybersathi.free-education.fun/  
Additional website: https://cybersathi.my.canva.site/

## Official Contact
- cybersathideelip@gmail.com
- jaiswaldilip8@gmail.com
- +91 8889473954

## Production Architecture
- Frontend: PHP + Bootstrap 5 + JavaScript
- Backend: Core PHP + PDO
- Database: MySQL (primary) + SQLite (local fallback)
- PDF: TCPDF integration in `certificate.php`
- Realtime chat: Firebase Firestore realtime stream with API fallback

## Security Implemented
- Google reCAPTCHA verification (register/complaint/volunteer forms)
- CSRF token generation and validation across forms and protected APIs
- Session auth for admin/user/volunteer roles
- Prepared statements and password hashing
- Upload type/size validation for complaint evidence

## Role-Based Dashboards
- User Dashboard: `user-dashboard.php` (quiz history + certificate download)
- Volunteer Dashboard: `volunteer-dashboard.php` (assigned fraud cases)
- Admin Dashboard: `admin/index.php` (approvals, assignments, event publishing)

## Setup
1. Import DB:
   ```bash
   mysql -u root -p < database/cybersathi.sql
   ```
2. Install TCPDF:
   ```bash
   composer install
   ```
3. Configure env vars (or webserver variables):
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`
   - `FIREBASE_API_KEY`, `FIREBASE_AUTH_DOMAIN`, `FIREBASE_PROJECT_ID`
4. Serve with Apache/Nginx + PHP 8.1+.

## Important Paths
- Public pages: `/index.php`, `/complaint.php`, `/quiz.php`, `/live-chat.php`
- Auth: `/login.php`, `/register.php`, `/volunteer-login.php`
- Dashboards: `/user-dashboard.php`, `/volunteer-dashboard.php`, `/admin/index.php`


## Automatic Bootstrap
- On startup, the app auto-creates required tables and seed records for both MySQL and SQLite modes.
- MySQL remains primary; SQLite fallback is used automatically when MySQL is unavailable.

## Super Admin Control
- Added WordPress-style admin shell UI with sidebar + analytics cards.
- Super Admin login (default): `superadmin@cybersathi.org / super123` (change immediately).
- Super Admin can create Admin/Super Admin users and has full dashboard control.
