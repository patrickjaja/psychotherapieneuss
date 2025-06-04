# Psychotherapie Neuss Website

Professional website for Dr. Pia Dornberger's psychotherapy practice in Neuss.

## Requirements

- PHP 8.2 or higher
- MariaDB/MySQL
- Composer
- Web server with mod_rewrite enabled

## Installation

1. Clone the repository or upload files to your web server

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file and configure it:
```bash
cp .env.example .env
```

Then edit `.env` with your database and SMTP credentials.

4. Run the database setup:
```bash
php setup.php
```

5. Point your web server document root to the `public/` directory

6. Ensure the web server can write to these directories:
```bash
chmod 755 var/
chmod 755 cache/
```

## Features

- Responsive design optimized for mobile devices
- Contact form with email notifications
- Email storage in MariaDB database
- Professional layout with TailwindCSS
- SEO-friendly URLs
- HTTPS redirect ready (uncomment in .htaccess when SSL certificate is installed)

## Structure

- `public/` - Web accessible files
- `src/` - Application source code
  - `Controller/` - Page controllers
  - `Service/` - Business logic services
- `templates/` - Twig templates
- `config/` - Configuration files
- `images/` - Website images

## Email Configuration

The contact form sends two emails:
1. Notification to the practice (info@psychotherapieneuss.de)
2. Confirmation to the sender

All emails are logged in the database for tracking.

## Security

- Environment variables for sensitive data
- Prepared statements for database queries
- CSRF protection on forms
- XSS protection in templates
- Security headers in .htaccess

## Maintenance

To check email logs:
```sql
SELECT * FROM contact_emails ORDER BY created_at DESC;
SELECT * FROM email_log ORDER BY created_at DESC;
```