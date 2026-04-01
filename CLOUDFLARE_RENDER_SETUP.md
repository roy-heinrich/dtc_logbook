# Cloudflare in Front of Render (DTC Logbook)

This guide configures Cloudflare as a reverse proxy/CDN in front of your Render web service.

## Target Architecture

- Browser -> Cloudflare edge -> Render web service -> Laravel app
- Supabase remains direct from Render (no Cloudflare in DB path)

## 1) DNS and Domain Setup

1. Add your domain to Cloudflare.
2. In Cloudflare DNS, create records pointing to Render:
   - `@` -> CNAME to your Render service hostname (flattening enabled by Cloudflare)
   - `www` -> CNAME to `@` or same Render hostname
3. Turn on the orange cloud (Proxied) for both records.
4. In Render, add the same custom domains under your web service settings.

## 2) SSL/TLS Configuration

In Cloudflare -> SSL/TLS:

- Set encryption mode to **Full (strict)**.
- Enable **Always Use HTTPS**.
- Enable **Automatic HTTPS Rewrites**.

Keep `APP_URL` as your production domain, not `onrender.com`.

## 3) Render Environment Variables

Set these in Render service environment:

```env
APP_URL=https://yourdomain.com
ASSET_URL=https://cdn.yourdomain.com   # optional; use if serving assets on subdomain
APP_ENV=production
APP_DEBUG=false
```

If you are not using a separate CDN hostname, set `ASSET_URL=https://yourdomain.com`.

## 4) Cloudflare Cache Rules (Recommended)

Create cache rules in Cloudflare (top to bottom):

1. **Do not cache admin/auth HTML**
   - Expression:
     - `http.request.uri.path contains "/admin"`
     - OR `http.request.uri.path contains "/login"`
   - Action: Bypass cache

2. **Cache static build assets aggressively**
   - Expression:
     - `http.request.uri.path starts_with "/build/"`
   - Action:
     - Cache eligibility: Eligible
     - Edge TTL: 1 month
     - Browser TTL: Respect origin (or 7 days)

3. **Cache public images/fonts**
   - Expression examples:
     - `http.request.uri.path starts_with "/images/"`
     - OR extensions: `webp`, `png`, `jpg`, `jpeg`, `svg`, `woff2`
   - Action:
     - Cache eligibility: Eligible
     - Edge TTL: 7 days

## 5) Cloudflare Speed Features

Enable:

- **Brotli** (Compression)
- **Early Hints** (optional)
- **HTTP/3 (QUIC)**

For minification, you already build minified assets via Vite; do **not** rely on Cloudflare auto-minify as your primary path.

## 6) WAF / Bot Protection

Use moderate defaults to avoid breaking admin login:

- WAF managed rules: On
- Bot fight mode: Optional, monitor first
- Add allowlist rules for admin IPs only if lockouts occur

## 7) Verify Correct Operation

Run checks after DNS propagates:

```bash
curl -I https://yourdomain.com
curl -I https://yourdomain.com/build/assets/frontend.js
curl -I https://yourdomain.com/admin
```

Expected:

- Static assets show `cf-cache-status: HIT` after warmup
- Admin pages should be `BYPASS`/`DYNAMIC`
- `content-encoding: br` appears when Brotli is applied

## 8) Notes for This Repository

- Laravel now trusts reverse-proxy forwarding headers in [bootstrap/app.php](bootstrap/app.php).
- HTML cache policy is controlled by [app/Http/Middleware/SetResponseCacheHeaders.php](app/Http/Middleware/SetResponseCacheHeaders.php):
  - Admin routes -> `no-store`
  - Public HTML -> short cache
- Static asset cache headers are still served by [public/.htaccess](public/.htaccess).

## 9) Rollback

If needed:

1. In Cloudflare DNS, disable proxy (gray cloud) temporarily.
2. Keep Render custom domain active.
3. Purge Cloudflare cache after rule changes.
