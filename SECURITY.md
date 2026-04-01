# Security Guide

## Important Security Information

This document outlines security best practices and configuration for the DTCLogbook application.

### ⚠️ CRITICAL: Environment Configuration

**Never commit the `.env` file to version control.** The `.env` file contains sensitive credentials and is excluded via `.gitignore`.

### Setup Instructions for Developers

1. **Create local `.env` file:**
   ```bash
   cp .env.example .env
   ```

2. **Generate application encryption key:**
   ```bash
   php artisan key:generate
   ```

3. **Generate JWT secret:**
   ```bash
   php artisan jwt:secret
   ```

4. **Configure your environment variables:**
   - Update `DB_*` variables with your local database credentials
   - Update `PHPMAILER_*` variables with your email service credentials
   - Update `APP_URL` with your local/production domain

### Security Requirements

#### 1. Environment Variables

All sensitive data **must** be stored in environment variables:

- `APP_KEY` - Application encryption key (auto-generated)
- `JWT_SECRET` - JWT signing secret (auto-generated)
- `DB_PASSWORD` - Database password
- `PHPMAILER_PASSWORD` - Email service password (use App Passwords, not plain password)
- `AWS_SECRET_ACCESS_KEY` - AWS secret key (if using AWS)

#### 2. Database Security

- Never commit database credentials to version control
- Use strong, unique passwords for database accounts
- For PostgreSQL/Supabase: Use connection pooling with SSL/TLS
- Example `.env` setup:
  ```
  DB_CONNECTION=pgsql
  DB_HOST=your-db-host.com
  DB_PORT=5432
  DB_DATABASE=your_db_name
  DB_USERNAME=secure_username
  DB_PASSWORD=strong_unique_password
  DB_SSLMODE=require
  ```

#### 3. Email Configuration

For Gmail SMTP:
1. Enable 2-factor authentication on your Google account
2. Generate an App Password: https://myaccount.google.com/apppasswords
3. Use the App Password (not your regular password) in `.env`:
   ```
   PHPMAILER_USERNAME=your-email@gmail.com
   PHPMAILER_PASSWORD=your-16-character-app-password
   ```

#### 4. Application Debug Mode

- **Local Development:** `APP_DEBUG=true`
- **Staging/Production:** `APP_DEBUG=false` (never expose debug information)

The `.env` file has been configured with `APP_DEBUG=false` for production safety.

#### 5. Logging Configuration

Logging levels are set to `error` in production to avoid logging sensitive information:

- **Development:** `LOG_LEVEL=debug`
- **Production:** `LOG_LEVEL=error`

Check `config/logging.php` for detailed logging configuration.

### Authentication Security

#### JWT (JSON Web Tokens)

The application uses JWT for API authentication via tymon/jwt-auth:

- `JWT_TTL` - Access token lifetime (minutes)
- `JWT_REFRESH_TTL` - Refresh token lifetime (minutes)
- `ADMIN_REFRESH_TOKEN_TTL_DAYS` - Admin refresh token validity

**Best Practices:**
- Regenerate `JWT_SECRET` periodically
- Use short-lived access tokens (default: 7 days)
- Implement token refresh mechanism
- Store tokens securely (never in localStorage for sensitive apps)

#### Password Security

- Minimum password length: 8 characters
- Passwords are hashed using bcrypt with `BCRYPT_ROUNDS=12`
- Password reset tokens are hashed (SHA-256) before storage

### CSRF Protection

The application implements CSRF protection via Laravel's middleware:

- CSRF tokens are required for state-changing requests (POST, PUT, DELETE)
- Include `@csrf` directive in all forms
- API requests use `X-CSRF-TOKEN` header

### SQL Injection Prevention

All database queries use Laravel's Eloquent ORM with parameterized queries:

- **Safe:** `User::where('email', $email)->first()`
- Database access is protected against SQL injection by default

### Authentication & Authorization

- Admin authentication uses JWT tokens with refresh token rotation
- Role-based access control (RBAC) via middleware
- Login rate limiting: 5 attempts per minute
- Password reset rate limiting: 3 attempts per hour

### File Upload Security

When implementing file uploads:

1. Validate file types (whitelist allowed extensions)
2. Store uploads outside web root
3. Rename files to prevent directory traversal
4. Scan uploads for malware (if applicable)
5. Set appropriate file permissions

### API Security Headers

Consider adding these headers in production:

```php
// In .htaccess or web server config
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: default-src 'self'
```

### Dependency Security

Regularly check for vulnerable dependencies:

```bash
# Check for vulnerable packages
composer audit

# Update dependencies
composer update
npm audit
npm update
```

### Production Deployment Checklist

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] `LOG_LEVEL=error` or `warning`
- [ ] Database using SSL/TLS connection
- [ ] Strong, unique database passwords
- [ ] `APP_KEY` and `JWT_SECRET` generated
- [ ] `.env` file NOT committed to git
- [ ] `.env` file has restrictive permissions (600)
- [ ] Cache cleared: `php artisan config:cache`
- [ ] Routes cached: `php artisan route:cache`
- [ ] Views compiled
- [ ] HTTPS enabled on web server
- [ ] CORS configured appropriately
- [ ] Rate limiting properly configured
- [ ] Error logging to secure location
- [ ] Regular security audits scheduled

### Reporting Security Vulnerabilities

If you discover a security vulnerability, please email security@example.com instead of creating a public GitHub issue.

### Additional Resources

- [Laravel Security Guide](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel JWT Auth Package](https://jwt-auth.readthedocs.io/)
- [Supabase Security](https://supabase.com/docs/guides/auth)

---

Last Updated: 2026-03-02
