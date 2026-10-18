# Deployment (cPanel shared hosting)

Status: written for P1. It has not been tried on the production host yet. Extend it as each module adds requirements (Paystack webhook URL in P4, file storage in P3).

## Requirements
- PHP 8.2+ with `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `bcmath`, `intl`
- MySQL 8 (or MariaDB 10.4+)
- Cron access
- HTTPS certificate (AutoSSL is fine)

## First deploy
1. Upload the project **outside** `public_html` (for example `~/otisam`). Point the domain's document root at `~/otisam/public`. If the host cannot change the document root, symlink `public_html` to `~/otisam/public`.
2. Build the assets locally with `npm run build`. `public/build` is committed, so no Node is needed on the server.
3. Run `composer install --no-dev --optimize-autoloader` (locally, then upload `vendor/`, if the host has no Composer).
4. Copy `.env.example` to `.env` and set:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`
   - the `DB_*` settings
   - `SMS_PROVIDER=speso`, `SPESO_API_KEY`, `SPESO_SMS_SENDER_ID` (a sender ID registered with Speso)
   - `MAIL_*` for the transactional email provider
   - `SESSION_SECURE_COOKIE=true`
   - `SEED_SUPERADMIN_*` for the first owner account
5. Generate the key and set up the database:
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force
   ```
6. Delete `SEED_SUPERADMIN_PASSWORD` from `.env` after the first sign-in. The account must change its password at first sign-in anyway.
7. Cache the configuration:
   ```bash
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```

## Cron (required)
Shared hosting cannot run a long-lived queue worker, so the scheduler runs every minute and drains the queue:

```
* * * * * cd ~/otisam && php artisan schedule:run >> /dev/null 2>&1
```

`routes/console.php` schedules `queue:work --stop-when-empty`. Queued SMS and email (approvals, receipts) therefore go out within about a minute. One-time codes are sent immediately, not through the queue.

## Updating
Upload the changed files, then run:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## Notes
- Staff are signed out after 30 idle minutes (`STAFF_IDLE_MINUTES`).
- Do not set `SMS_PROVIDER=log` in production: codes would only be written to the log.
- Backups (nightly database dump + weekly files, stored off-server) are set up in P11.
