# Render Deployment - Quick Reference

## 📋 Pre-Deployment Checklist

- [ ] All code committed to GitHub `main` branch
- [ ] `.env` file is NOT committed (check `.gitignore`)
- [ ] Review [SECURITY.md](SECURITY.md) for security requirements
- [ ] Review [RENDER_SETUP.md](RENDER_SETUP.md) for detailed setup
- [ ] Created Render account at [render.com](https://render.com)

## 🚀 Deploy in 5 Minutes

### 1. Create PostgreSQL Database (2 min)
```
Dashboard → + New → PostgreSQL
Name: dtc-logbook-db
(Copy connection details after creation)
```

### 2. Create Web Service (2 min)
```
Dashboard → + New → Web Service
- Connect GitHub repo
- Render auto-detects render.yaml
- Add environment variables (see below)
- Deploy!
```

### 3. Set Environment Variables (1 min)

Add these in Render Web Service settings:

```bash
# App Configuration
APP_ENV=production
APP_DEBUG=false
APP_KEY=                    # Auto-generated during build
APP_URL=https://your-render-domain.onrender.com

# Database (from PostgreSQL database page)
DB_CONNECTION=pgsql
DB_HOST=<copy-from-postgres-details>
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=<copy-from-postgres-details>
DB_PASSWORD=<copy-from-postgres-details>
DB_SSLMODE=require

# JWT
JWT_SECRET=                 # Auto-generated during build
JWT_TTL=10080
JWT_REFRESH_TTL=20160

# Email (Gmail example - customize for your email service)
PHPMAILER_HOST=smtp.gmail.com
PHPMAILER_PORT=587
PHPMAILER_USERNAME=your-email@gmail.com
PHPMAILER_PASSWORD=your-16-char-app-password
PHPMAILER_ENCRYPTION=tls
PHPMAILER_FROM_ADDRESS=your-email@gmail.com
PHPMAILER_FROM_NAME="DTC Logbook"

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 4. Post-Deployment (Run in Render Shell)
```bash
php artisan migrate --force
php artisan db:seed --force  # Optional
```

### 5. Verify Deployment
```
Visit: https://your-service-name.onrender.com
Admin Panel: https://your-service-name.onrender.com/admin/login
```

## 🔗 Useful Links

| Resource | Description |
|----------|-------------|
| [Render Docs](https://render.com/docs) | Official Render documentation |
| [RENDER_SETUP.md](RENDER_SETUP.md) | Detailed setup guide with troubleshooting |
| [SECURITY.md](SECURITY.md) | Security guidelines and best practices |
| [DEPLOYMENT.md](DEPLOYMENT.md) | General deployment checklist |

## 📧 Gmail App Password Setup

For PHPMailer/Gmail:

1. Go to [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
2. Select "Mail" and "Windows Computer" (or your OS)
3. Generate 16-character app password
4. Use this password in `PHPMAILER_PASSWORD` (NOT your regular password)

## 🚨 Common Issues

### Build Fails
- Check PHP version compatibility (8.2+)
- View logs in Render Dashboard
- Run `composer audit` and `npm audit` locally

### Database Connection Error
- Verify `DB_PASSWORD` is correctly set
- Ensure `DB_SSLMODE=require`
- Check database is accepting connections

### App Won't Start
- Check `APP_KEY` is set (should auto-generate)
- Verify `JWT_SECRET` is set
- Check logs in Render Dashboard

### Can't Connect to Email
- Generate Gmail App Password (not regular password)
- Enable 2FA on Google account
- Verify `PHPMAILER_*` variables

## 💡 Tips

1. **Monitor Logs:** Always check Render Dashboard logs when something fails
2. **Free Tier Limits:** Remember free tier has 512 MB RAM - optimize as needed
3. **Custom Domain:** Add custom domain in Render settings after deployment
4. **Backups:** Set up PostgreSQL backups in Render dashboard
5. **Auto-deploy:** Enable auto-deploy from main branch for CI/CD

## 📞 Need Help?

1. **Render Issues:** Check [Render Docs](https://render.com/docs) or contact support
2. **Laravel Issues:** Check [Laravel Docs](https://laravel.com/docs)
3. **Security:** See [SECURITY.md](SECURITY.md#reporting-security-vulnerabilities)
4. **Deployment:** Review [RENDER_SETUP.md](RENDER_SETUP.md)

---

**Deploy with confidence! Your app is secure and ready for production.** ✅

*Last Updated: 2026-03-02*
