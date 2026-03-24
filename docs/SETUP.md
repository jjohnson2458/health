# Claude Health - Setup Guide

## Prerequisites

| Requirement       | Version  | Notes                                 |
|-------------------|----------|---------------------------------------|
| PHP               | 8.1+     | With `openssl`, `pdo_mysql`, `mbstring`, `json` extensions |
| MySQL / MariaDB   | 8.0+ / 10.6+ | InnoDB engine required             |
| Composer          | 2.x      | PHP dependency manager                |
| Node.js           | 18+      | For Playwright tests and frontend tooling |
| XAMPP             | 8.1+     | Local development (Apache + MySQL bundle) |
| Git               | 2.x      | Version control                       |

---

## 1. Clone the Repository

```bash
cd C:\xampp\htdocs
git clone <repository-url> claude_health
cd claude_health
```

---

## 2. Install Dependencies

```bash
# PHP dependencies
composer install

# Node dependencies (for Playwright tests)
npm install
npx playwright install
```

---

## 3. Database Setup

### Create the database

```sql
CREATE DATABASE claude_health CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Import the schema

```bash
mysql -u root claude_health < database/schema.sql
```

Or via phpMyAdmin:

1. Open http://localhost/phpmyadmin
2. Create database `claude_health` with `utf8mb4_unicode_ci` collation
3. Select the database, go to **Import**, and upload `database/schema.sql`

### Run migrations (if any pending)

```bash
php scripts/migrate.php
```

---

## 4. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and configure the required values:

```dotenv
APP_NAME=ClaudeHealth
APP_ENV=local
APP_URL=http://health.local

# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=claude_health
DB_USERNAME=root
DB_PASSWORD=

# Encryption key - REQUIRED for HIPAA compliance
# Generate with: php -r "echo bin2hex(random_bytes(32));"
ENCRYPTION_KEY=<your-64-char-hex-string>

# Session
SESSION_LIFETIME=30
SESSION_SECURE=false

# Features
TWOFA_ENABLED=true
NEW_USER_REGISTRATION=true

# Email (leave SMTP_HOST blank to use claude_messenger for local dev)
SMTP_HOST=
SMTP_PORT=587
SMTP_USERNAME=
SMTP_PASSWORD=
SMTP_FROM_EMAIL=
SMTP_REPLY_DOMAIN=

# Local dev fallback
MAIL_NOTIFY_SCRIPT=C:/xampp/htdocs/claude_messenger/notify.php
MAIL_FROM_NAME=ClaudeHealth
```

### Generate the encryption key

This is critical. All PHI (Protected Health Information) is encrypted at rest using this key.

```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

Copy the output into your `.env` file as `ENCRYPTION_KEY`.

> **WARNING:** Losing this key means all encrypted data (names, emails, health entries, medication details) becomes permanently unrecoverable. Back it up securely.

---

## 5. Apache Virtual Host Configuration

Add the following to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName health.local
    DocumentRoot "C:/xampp/htdocs/claude_health/public"

    <Directory "C:/xampp/htdocs/claude_health/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/claude_health-error.log"
    CustomLog "logs/claude_health-access.log" common
</VirtualHost>
```

Add the hostname to your Windows hosts file (`C:\Windows\System32\drivers\etc\hosts`):

```
127.0.0.1    health.local
```

Restart Apache via the XAMPP Control Panel.

Verify by navigating to http://health.local in your browser.

---

## 6. Create the Admin Account

```bash
php scripts/create-admin.php
```

Default credentials:
- **Username:** admin
- **Password:** Admin123!

> Change the admin password immediately after first login via the admin panel.

---

## 7. Running Locally

1. Start Apache and MySQL from XAMPP Control Panel
2. Navigate to http://health.local
3. Log in with admin credentials or register a new account

### Development workflow

- PHP files are served directly by Apache (no build step needed)
- Changes to PHP files take effect immediately on page refresh
- The application log is available in XAMPP's Apache error log

---

## 8. Running Tests

### PHPUnit (Unit + Integration Tests)

```bash
# Run all tests
vendor/bin/phpunit

# Run with coverage report
vendor/bin/phpunit --coverage-html docs/coverage

# Run a specific test file
vendor/bin/phpunit tests/Unit/EncryptionTest.php
```

Configuration is in `phpunit.xml` at the project root.

### Playwright (End-to-End Tests)

```bash
# Run all E2E tests
npx playwright test

# Run with browser UI
npx playwright test --headed

# Run a specific test
npx playwright test tests/e2e/login.spec.ts

# View test report
npx playwright show-report
```

Configuration is in `playwright.config.ts` at the project root.

---

## 9. Generating API Documentation

A custom documentation generator is included (no phpDocumentor installation required):

```bash
php scripts/generate-docs.php
```

Output is written to `docs/phpdoc/index.html`. Open it in a browser to view.

If phpDocumentor is available, you can also use:

```bash
phpDocumentor --config phpdoc.dist.xml
```

---

## 10. Deployment to Production

### Server requirements

- PHP 8.1+ with required extensions
- MySQL 8.0+
- Apache with `mod_rewrite` enabled
- SSL certificate (required for HIPAA compliance)
- Firewall rules restricting database access

### Deploy via git + SSH

```bash
# On the production server
cd /var/www/claude_health
git pull origin main
composer install --no-dev --optimize-autoloader

# Run any pending migrations
php scripts/migrate.php
```

### Production `.env` adjustments

```dotenv
APP_ENV=production
APP_URL=https://your-domain.com
SESSION_SECURE=true

# Direct SMTP (no claude_messenger fallback in production)
SMTP_HOST=smtp.your-provider.com
SMTP_PORT=587
SMTP_USERNAME=your-email@domain.com
SMTP_PASSWORD=your-smtp-password
SMTP_FROM_EMAIL=noreply@your-domain.com
SMTP_REPLY_DOMAIN=your-domain.com
```

### Post-deployment checklist

- [ ] SSL certificate is valid and enforced (HTTPS only)
- [ ] `APP_ENV=production`
- [ ] `SESSION_SECURE=true`
- [ ] `.env` file permissions are `600` (owner read/write only)
- [ ] `ENCRYPTION_KEY` is set and backed up securely
- [ ] Database credentials use a non-root user with minimal privileges
- [ ] Error display is disabled (`display_errors = Off` in php.ini)
- [ ] Apache directory listing is disabled
- [ ] Log files are not publicly accessible

---

## 11. HIPAA Compliance Notes

Claude Health is designed with HIPAA technical safeguards in mind. The following measures are implemented:

### Encryption at rest

All Protected Health Information (PHI) is encrypted using AES-256-CBC before storage:

- **User data:** first name, last name, email address
- **Health entries:** weight, notes
- **Medications:** name, dosage, prescriber name, discontinuation reason, notes
- **Appointments:** provider name, location, notes

Email addresses are also hashed (SHA-256) for lookup without decryption.

### Access controls

- Password hashing uses bcrypt with cost factor 12
- Optional two-factor authentication via email codes
- Session timeout after configurable inactivity period (default: 30 minutes)
- Session IDs are regenerated after authentication
- CSRF protection on all POST requests
- Login rate limiting (5 attempts per 15-minute window)
- Role-based access control (user / admin)

### Audit logging

Every data access, modification, export, and authentication event is recorded in the `audit_log` table with:

- User ID
- Action performed
- Resource accessed
- Detailed description
- IP address
- Timestamp

### Error handling

Application errors are captured in the `error_log` table (not exposed to users) with:

- Error level, message, file, line number
- Stack trace
- User ID and IP address
- Request URL

### Data export

- Patients can export their own data as CSV
- Admin can export individual patient records
- All exports are logged in the audit trail

### Additional recommendations for production

- Enable HTTPS with TLS 1.2+ (mandatory for HIPAA)
- Configure HTTP security headers (HSTS, CSP, X-Frame-Options)
- Implement database backup encryption
- Set up log rotation for audit logs
- Execute a Business Associate Agreement (BAA) with your hosting provider
- Conduct regular security assessments and penetration testing
- Document your Security Risk Assessment per HIPAA requirement

---

## 12. Project Structure

```
claude_health/
├── app/
│   ├── Controllers/      # HTTP request handlers
│   ├── Helpers/           # Global utility functions
│   ├── Lang/              # Translation files (en, es)
│   ├── Middleware/         # Request filters (auth, CSRF, admin)
│   ├── Models/            # Data access with encryption
│   └── Views/             # PHP templates
├── config/                # Application configuration
├── core/                  # Framework classes (Router, Database, etc.)
├── database/
│   ├── schema.sql         # Full database schema
│   └── migrations/        # Incremental migrations
├── docs/
│   ├── SETUP.md           # This file
│   └── phpdoc/            # Generated API documentation
├── public/                # Web root (index.php, assets)
├── scripts/
│   ├── create-admin.php   # Admin account creation
│   ├── deploy.sh          # Deployment script
│   ├── generate-docs.php  # Documentation generator
│   └── migrate.php        # Database migration runner
├── tests/                 # PHPUnit and Playwright tests
├── .env.example           # Environment template
├── composer.json          # PHP dependencies
├── phpunit.xml            # PHPUnit configuration
├── playwright.config.ts   # Playwright configuration
└── phpdoc.dist.xml        # phpDocumentor configuration
```
