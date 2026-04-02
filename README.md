# DTC Logbook Admin System

A comprehensive Laravel-based logging and activity tracking system designed for managing facility operations, user activities, and generating detailed reports.

## Features

- **User Management:** Register, track, and manage users with role-based access control
- **Activity Logging:** Record and track all user activities with detailed timestamps
- **Admin Dashboard:** Real-time statistics and system monitoring
- **Report Generation:** Export activities to Excel, PDF, and CSV formats
- **Facility Management:** Organize and manage multiple facilities
- **Authentication:** Secure JWT-based API authentication with refresh tokens
- **Email Notifications:** Integrated email service for alerts and notifications
- **Dark Mode:** Responsive UI with dark/light theme support

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Vite, Tailwind CSS, Alpine.js
- **Database:** PostgreSQL (with SQLite support for development)
- **Authentication:** JWT (tymon/jwt-auth)
- **PDF Generation:** DomPDF
- **Email:** PHPMailer & Laravel Mail
- **Testing:** PHPUnit

## Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- PostgreSQL (production) or SQLite (development)
- Git

### Local Development Setup

1. **Clone the repository:**
   ```bash
   git clone <your-repo-url>
   cd DTCLogbook
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

4. **Database setup:**
   ```bash
   php artisan migrate
   php artisan db:seed  # Optional: seed initial data
   ```

5. **Build frontend:**
   ```bash
   npm run build
   # or for development with watch mode:
   npm run dev
   ```

6. **Start development server:**
   ```bash
   php artisan serve
   ```

   Visit: [http://localhost:8000/admin/login](http://localhost:8000/admin/login)

### Development Commands

```bash
# Run both server and frontend dev with hot reload
composer run dev

# Build production assets
npm run build

# Run tests
composer test

# Code formatting
composer run lint

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Deployment

### Render (Recommended)

See [RENDER_SETUP.md](RENDER_SETUP.md) for complete deployment guide.
For Cloudflare in front of Render, see [CLOUDFLARE_RENDER_SETUP.md](CLOUDFLARE_RENDER_SETUP.md).

This repository includes a portable Render Blueprint in [render.yaml](render.yaml) that provisions:
- `dtc-logbook` (main Laravel web service)
- `dtc-logbook-websocket` (Socket.IO service)
- `dtc-logbook-scheduler` (Laravel scheduler cron)
- `dtc-logbook-redis` (managed Redis/Key Value)

When creating from Blueprint, Render will prompt for `sync: false` variables (for example `WEBSOCKET_PUBLIC_URL` and `WEBSOCKET_ALLOWED_ORIGINS`).

Quick start:
1. Push code to GitHub
2. Create account on [render.com](https://render.com)
3. Create PostgreSQL database on Render
4. Create Web Service and connect GitHub repo
5. Set environment variables
6. Deploy!

### Render + Cloudflare (Recommended for Performance)

1. Put your custom domain behind Cloudflare proxy (orange cloud)
2. Keep SSL mode at **Full (strict)**
3. Set `APP_URL` to your domain and `ASSET_URL` to your CDN/domain
4. Cache only static assets (`/build/*`, `/images/*`), bypass `/admin/*`
5. Enable Brotli + HTTP/3 in Cloudflare

### Other Platforms

See [DEPLOYMENT.md](DEPLOYMENT.md) for comprehensive deployment checklist covering:
- Heroku
- DigitalOcean
- AWS
- Docker
- Laravel Forge
- Traditional VPS

## Configuration

### Environment Variables

Essential environment variables (see `.env.example` for full list):

```env
APP_ENV=production              # or 'local' for development
APP_DEBUG=false                 # Never true in production
APP_KEY=                        # Generated automatically
JWT_SECRET=                     # Generated automatically

DB_CONNECTION=pgsql
DB_HOST=your-database-host      # PostgreSQL host
DB_DATABASE=your_database       # Database name
DB_USERNAME=postgres            # Database user
DB_PASSWORD=your_password       # Database password

PHPMAILER_HOST=smtp.gmail.com   # Email service
PHPMAILER_USERNAME=your@email   # Your email
PHPMAILER_PASSWORD=app_password # Gmail App Password
```

See [RENDER_SETUP.md](RENDER_SETUP.md#step-3-configure-environment-variables) for Render-specific configuration.

## Project Structure

```
app/
  ├── Http/
  │   ├── Controllers/          # API & Web controllers
  │   ├── Middleware/           # Authentication & authorization
  │   └── Requests/             # Form validation
  ├── Models/                   # Database models (Eloquent)
  └── Services/                 # Business logic
config/                         # Application configuration
database/
  ├── migrations/               # Database schema
  └── seeders/                  # Initial data
resources/
  ├── views/                    # Blade templates
  ├── css/                      # Tailwind CSS
  └── js/                       # Vue/Alpine components
routes/
  ├── api.php                   # API routes (JWT protected)
  ├── web.php                   # Web routes (session auth)
  └── auth.php                  # Authentication routes
```

## API Documentation

### Authentication Endpoints

```
POST   /api/admin/auth/login        # Admin login
POST   /api/admin/auth/refresh      # Refresh JWT token
POST   /api/admin/auth/logout       # Logout (requires token)
```

### Usage Example

```bash
# Login
curl -X POST https://your-app.com/api/admin/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Use returned token in Authorization header
curl -X GET https://your-app.com/api/users \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Security

This application implements multiple security measures:

- ✅ Secure password hashing (bcrypt)
- ✅ JWT token authentication with refresh rotation
- ✅ CSRF protection on web routes
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ Rate limiting on authentication endpoints
- ✅ Environment variable for sensitive data
- ✅ HTTPS in production
- ✅ Password reset with email verification

**Important:** See [SECURITY.md](SECURITY.md) for detailed security guidelines before deployment.

## Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AdminAuthTest.php

# Run with coverage
php artisan test --coverage

# Format code
composer run lint
```

## Troubleshooting

### Database Connection Issues

```bash
# Test database connection
php artisan db

# Check database configuration
php artisan tinker
# Then: config('database.connections.pgsql')
```

### Storage & File Uploads

```bash
# Create storage link for uploads
php artisan storage:link

# Check permissions
ls -la storage/
```

### Cache Issues

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
```

### Email Not Sending

1. Check `PHPMAILER_*` environment variables
2. For Gmail: Use [App Password](https://myaccount.google.com/apppasswords), not regular password
3. Enable "Less secure app access" if using non-Business Gmail
4. Check logs: `storage/logs/laravel.log`

## Contributing

1. Create a feature branch: `git checkout -b feature/your-feature`
2. Make changes and commit: `git commit -am 'Add feature'`
3. Push branch: `git push origin feature/your-feature`
4. Submit Pull Request

## Support & Issues

For bug reports and feature requests, open an issue on GitHub.

For security vulnerabilities, see [SECURITY.md](SECURITY.md#reporting-security-vulnerabilities)

## License

This project is licensed under the MIT License - see [LICENSE](LICENSE) file for details.
