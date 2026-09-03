# Meridian Systems — Corporate Website

A production-ready corporate website with a public marketing site, secure
client portal, analytics dashboard, and an AI-assistant chat interface.
Built with HTML5, CSS3, vanilla JavaScript (ES6+), Bootstrap 5, PHP 8+, and
MySQL 8+ — no Node.js, Composer, or build step required.

## 1. Requirements

- Apache (or Nginx) with PHP 8.0+ and the following extensions enabled:
  `pdo_mysql`, `mbstring` (both are on by default on virtually all cPanel
  and XAMPP installs, but confirm if you're on a minimal/custom PHP build)
- MySQL 8.0+ / MariaDB 10.4+
- Any standard shared hosting, cPanel, or local XAMPP/MAMP install works

## 2. Folder structure

```
project/
├── index.php                Home page
├── about.php                About Us page
├── team.php                 Team page (dynamic member list)
├── contact.php               Contact Us page + form handler
├── client/
│   ├── login.php             Client login
│   ├── dashboard.php         Client dashboard (Chart.js)
│   ├── dashboard-data.php    JSON data endpoint for dashboard charts
│   ├── ai-solution.php       AI chat interface
│   ├── ai-response.php       JSON endpoint powering the chat
│   └── logout.php            Session termination
├── includes/
│   ├── config.php            DB credentials + session hardening (EDIT THIS)
│   ├── auth.php               Login-required middleware
│   ├── functions.php          Shared helpers (CSRF, sanitization, etc.)
│   ├── header.php             Shared navbar
│   └── footer.php             Shared footer
├── database/
│   └── schema.sql             Full schema + demo seed data
├── assets/
│   ├── css/style.css
│   ├── js/ (main.js, contact-form.js, dashboard.js, ai-solution.js)
│   └── images/
└── logs/                      App error log (auto-created, web-inaccessible)
```

## 3. Deployment steps

1. **Create a database** in MySQL/cPanel (e.g. `corporate_site`).
2. **Import the schema:**
   ```bash
   mysql -u your_db_user -p your_db_name < database/schema.sql
   ```
   This creates the `clients`, `feedback`, and `contact_messages` tables and
   seeds one demo client account plus sample feedback rows.
3. **Edit `includes/config.php`** and set your real credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_db_name');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   ```
   Also set `APP_DEBUG` to `false` in production (it already defaults to
   `false`).
4. **Upload all files** to your web root (e.g. `public_html/` on cPanel, or
   `htdocs/` in XAMPP) via FTP/SFTP or the cPanel File Manager.
5. **Set folder permissions** so PHP can write to `logs/` (typically `755`
   for directories is sufficient on shared hosting).
6. **Visit the site** — no further build steps, `npm install`, or Composer
   commands are needed.

## 4. Demo client login

The seed data creates one demo account so you can test the portal immediately:

- **Client ID:** `democlient`
- **Password:** `Demo@12345`

**Change or remove this account before going live.** To create additional
clients, insert a row into `clients` with a password hashed via PHP:

```php
<?php
echo password_hash('the_new_password', PASSWORD_DEFAULT);
```

Copy the resulting hash into the `password` column.

## 5. Security features implemented

- Prepared statements (PDO) for every database query — no raw SQL
  concatenation anywhere in the codebase.
- Passwords hashed with `password_hash()` / verified with `password_verify()`
  — no plain-text passwords are ever stored or compared.
- CSRF tokens on every form (`contact.php`, `client/login.php`) and on the
  AI chat AJAX endpoint.
- Output escaping via a shared `e()` helper (XSS protection) on every
  user-controlled value rendered to HTML.
- Session hardening: `httponly` and `samesite` cookies, automatic `secure`
  flag over HTTPS, session ID regeneration on login, and an idle session
  timeout (30 minutes by default, configurable in `config.php`).
- A simple login rate limiter that locks out further attempts for 60
  seconds after 5 consecutive failures.
- `.htaccess` rules blocking direct access to `includes/`, `database/`,
  and `logs/`, plus baseline security headers.

## 6. Extending the AI Solution page

`client/ai-response.php` currently returns rule-based canned replies so the
chat works fully out of the box. To connect a real model (e.g. the OpenAI
API), replace the body of `generateAssistantReply()` with an HTTP call to
your provider of choice — the JSON request/response contract with the
frontend (`assets/js/ai-solution.js`) does not need to change. Store your
API key as an environment variable or in a config value outside of version
control; never hard-code it in a committed file.

## 7. Adding team members

Edit the `$teamMembers` array at the top of `team.php` — the card grid
below it renders dynamically from that array, so no HTML changes are
required to add, remove, or reorder members.

## 8. Notes on images

Placeholder images are included in `assets/images/` (hero background and
four team portraits) so the site renders correctly immediately after
deployment. Replace them with real photography before launch.
