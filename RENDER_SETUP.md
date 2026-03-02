# Render Deployment Guide

This guide walks you through deploying the DTCLogbook application to [Render](https://render.com).

## Prerequisites

- Render account (free tier available at https://render.com)
- GitHub repository with this code pushed
- PostgreSQL database (Render can provide one)
- Environment variables configured

## Step 1: Create a PostgreSQL Database on Render

1. Go to [Render Dashboard](https://dashboard.render.com)
2. Click **"+ New"** → **"PostgreSQL Database"**
3. Configure:
   - **Name:** `dtc-logbook-db`
   - **Database:** `postgres`
   - **User:** Will be auto-generated
   - **Region:** Choose closest to your users
   - **Plan:** Choose based on your needs (pay-as-you-go, free tier available)
4. Click **"Create Database"**
5. Wait for database to be created (copy the connection string)

Database connection string format:
```
postgresql://user:password@host:5432/database
```

## Step 2: Deploy Web Service on Render

### Option A: Using Render Dashboard

1. Click **"+ New"** → **"Web Service"**
2. Connect your GitHub repository:
   - Click **"Connect"** next to your repo
   - Authorize Render to access your GitHub
3. Configure the service:
   - **Name:** `dtc-logbook`
   - **Environment:** `PHP`
   - **Region:** Same as database (recommended)
   - **Plan:** Free (or higher)
   - **Build Command:** *(Leave empty - uses buildCommand from render.yaml)*
   - **Start Command:** *(Leave empty - uses startCommand from render.yaml)*
4. Add environment variables (see "Step 3" below)
5. Click **"Deploy"**

### Option B: Using GitHub Integration

1. Push `render.yaml` to your repository
2. Go to Render Dashboard → New → Web Service
3. Select your GitHub repository
4. Render will automatically detect `render.yaml` and use those settings

### Option C: Using Infrastructure as Code (IaC)

```bash
# If using Render CLI
render deploy --service dtc-logbook
```

## Step 3: Configure Environment Variables

In Render Dashboard, go to your web service settings and add these environment variables:

### Required Variables

```
APP_KEY=                    # Leave empty, will be generated during build
APP_ENV=production          # Important: Must be production
APP_DEBUG=false             # Never true in production
APP_URL=https://your-service-name.onrender.com  # Replace with your Render URL

# Database (from PostgreSQL database credentials)
DB_CONNECTION=pgsql
DB_HOST=<your-db-host>       # From database credentials
DB_PORT=5432
DB_DATABASE=postgres         # Or your database name
DB_USERNAME=<your-db-user>   # From database credentials
DB_PASSWORD=<your-db-password> # From database credentials
DB_SSLMODE=require           # Important: Render requires SSL

# JWT Configuration
JWT_SECRET=                  # Leave empty or generate: openssl rand -base64 32
JWT_TTL=10080                # 7 days in minutes
JWT_REFRESH_TTL=20160        # 14 days in minutes

# Email Configuration (Gmail example)
PHPMAILER_HOST=smtp.gmail.com
PHPMAILER_PORT=587
PHPMAILER_USERNAME=your-email@gmail.com
PHPMAILER_PASSWORD=your-app-password  # Use Gmail App Password, not regular password
PHPMAILER_ENCRYPTION=tls
PHPMAILER_FROM_ADDRESS=your-email@gmail.com
PHPMAILER_FROM_NAME="DTC Logbook"

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error              # Production should be error or warning

# Session & Cache
SESSION_DRIVER=file
CACHE_STORE=file             # Or redis if using caching
QUEUE_CONNECTION=sync        # sync for free tier, database if scaled

# Asset URL (Optional, for CDN)
ASSET_URL=https://your-service-name.onrender.com
```

## Step 4: Set Build & Start Commands (if not using render.yaml)

If Render doesn't auto-detect `render.yaml`:

**Build Command:**
```bash
composer install --optimize-autoloader --no-dev && php artisan config:cache && php artisan route:cache && npm install && npm run build
```

**Start Command:**
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

## Step 5: Run Database Migrations

After deployment, you need to run migrations:

### Option A: Using Render Shell

1. Go to your service on Render Dashboard
2. Click **"Shell"** tab
3. Run:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force  # If you have seeders
   ```

### Option B: Using Release Command (Recommended)

Add to your Procfile or configure in Render:
```
release: php artisan migrate --force
```

This runs automatically before starting the web service.

## Step 6: Verify Deployment

1. Visit your Render service URL: `https://<your-service-name>.onrender.com`
2. Check logs:
   - In Render Dashboard → Your service → **Logs** tab
   - Look for any errors during startup
3. Test endpoints:
   - Admin login: `https://<your-service-name>.onrender.com/admin/login`
   - API health: `https://<your-service-name>.onrender.com/api/health`

## Troubleshooting

### Build Fails

1. Check build logs in Render Dashboard
2. Common issues:
   - **Composer timeout:** Increase timeout in composer.json
   - **Node modules:** Clear cache and rebuild
   - **Missing extensions:** Check PHP version and extensions

```bash
# Check PHP extensions
php -m

# Check PHP version
php --version
```

### Database Connection Error

1. Verify database credentials in environment variables
2. Ensure `DB_SSLMODE=require` is set
3. Check database is accepting connections
4. Run in Render Shell:
   ```bash
   php artisan db
   ```

### Application Won't Start

1. Check environment variables are set correctly
2. Verify `APP_KEY` is generated:
   ```bash
   php artisan key:generate
   ```
3. Check logs for specific errors
4. Clear cache:
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

### Assets Not Loading

1. Verify `npm run build` completed successfully
2. Check `ASSET_URL` is correct
3. Run:
   ```bash
   php artisan storage:link
   ```

## Performance Optimization

### For Free Tier

1. **Connection pooling:** Use PgBouncer (Render provides)
2. **Caching:** Use file-based cache store
3. **Queue:** Use sync driver for small apps
4. **Logging:** Set to `warning` or `error` level
5. **Disable unused services:** Remove Redis if not using

### For Paid Tier

1. **Database:** Upgrade PostgreSQL plan
2. **Web service:** Use Standard or higher plan
3. **Caching:** Use Redis
4. **Queue:** Use database or Redis queue driver
5. **CDN:** Consider Cloudflare for assets

## Environment-Specific Configuration

### Development (Local)

```bash
APP_ENV=local
APP_DEBUG=true
LOG_LEVEL=debug
DB_CONNECTION=sqlite
```

### Production (Render)

```bash
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
DB_CONNECTION=pgsql
DB_SSLMODE=require
```

## Updating Application

### Deploying New Changes

1. Push changes to GitHub:
   ```bash
   git add .
   git commit -m "Your commit message"
   git push origin main
   ```

2. Render automatically redeploys (if auto-deploy is enabled)

3. Or manually trigger deployment:
   - Render Dashboard → Your service → **Deploys** tab → **Trigger Deploy**

### Clearing Cache After Update

```bash
# In Render Shell
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Security Considerations

1. ✅ Never set `APP_DEBUG=true` in production
2. ✅ Use strong database passwords
3. ✅ Enable HTTPS (automatic on Render)
4. ✅ Use Gmail App Passwords, not regular passwords
5. ✅ Regenerate `APP_KEY` and `JWT_SECRET` periodically
6. ✅ Keep dependencies updated (`composer update`, `npm update`)
7. ✅ Monitor logs for suspicious activity
8. ✅ Use environment variables for all secrets

## File Size Limits

Render free tier has limitations:
- **Deployment size:** Up to 500 MB
- **Ephemeral disk:** 1 GB
- Files stored in `/tmp`, `/runs`, `/dev/shm` are temporary

**Important:** Don't store uploaded files on Render's filesystem. Use:
- S3/AWS for file storage
- Render's PostgreSQL for file metadata
- Cloudinary or similar for images

## Next Steps

1. Set up a `.env.production` locally (don't commit)
2. Configure continuous deployment from main branch
3. Set up monitoring/alerting
4. Schedule database backups
5. Consider custom domain configuration
6. Set up CI/CD pipeline for tests before deploy

## Additional Resources

- [Render Documentation](https://render.com/docs)
- [Laravel on Render](https://render.com/docs/deploy-laravel)
- [PostgreSQL on Render](https://render.com/docs/postgres)
- [Environment Variables Guide](https://render.com/docs/environment-variables)

---

**Support:** For issues, check Render docs or contact Render support at support@render.com

**Last Updated:** 2026-03-02
