# Hostinger Deployment Guide for Laravel POS

## Prerequisites
- Hostinger hosting account with PHP 7.4+ support
- MySQL database created in cPanel
- SSH access (optional but recommended)
- Git installed on hosting (if using Git deployment)

## Step 1: Prepare Your Files

### Option A: Upload via FTP/File Manager
1. Compress your entire project into a ZIP file (exclude `node_modules` and `vendor` folders to save time)
2. Upload to your Hostinger account
3. Extract in the root directory (usually `public_html` or `domains/yourdomain.com`)

### Option B: Deploy via Git (Recommended)
1. SSH into your Hostinger account
2. Navigate to your domain directory
3. Clone your repository:
   ```bash
   git clone <your-repo-url> .
   ```

## Step 2: Install Dependencies

SSH into your hosting and run:
```bash
composer install --optimize-autoloader --no-dev
```

If composer is not available globally, use:
```bash
php composer.phar install --optimize-autoloader --no-dev
```

## Step 3: Configure Environment

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` file with your Hostinger details:
   ```
   APP_NAME="Your POS Name"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password

   SESSION_DRIVER=file
   CACHE_DRIVER=file
   QUEUE_CONNECTION=database
   ```

3. Generate application key:
   ```bash
   php artisan key:generate
   ```

## Step 4: Set Up Public Directory

Hostinger requires your Laravel `public` folder to be the web root.

### Method 1: Using .htaccess (if files are in public_html)
Create/update `.htaccess` in `public_html`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Method 2: Change Document Root (Recommended)
1. Log into cPanel
2. Go to "Domains" or "Advanced" → "Domain Manager"
3. Click "Manage" next to your domain
4. Change "Document Root" to point to `/public_html/public` (or wherever your public folder is)
5. Save changes

## Step 5: Set Directory Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/framework
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache
```

## Step 6: Run Migrations

```bash
php artisan migrate --force
```

If you need to seed data:
```bash
php artisan db:seed --force
```

## Step 7: Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 8: Create Database (if not done)

1. Log into cPanel
2. Go to "MySQL Databases"
3. Create a new database
4. Create a database user with a strong password
5. Add the user to the database with ALL PRIVILEGES
6. Note down the database name, username, and password for `.env`

## Troubleshooting

### 500 Internal Server Error
- Check `.env` file exists and is configured correctly
- Verify storage and cache directories are writable
- Check error logs in cPanel or `storage/logs/laravel.log`
- Make sure `APP_KEY` is set in `.env`

### Database Connection Issues
- Verify database credentials in `.env`
- Check if database host is `localhost` or an IP address
- Ensure database user has proper privileges

### Missing Dependencies
- Run `composer install` again
- Check PHP version compatibility (PHP 7.4+ required)

### Permission Denied Errors
- Re-run the chmod commands for storage and cache directories
- Contact Hostinger support if issues persist

### White Screen / No Errors
- Set `APP_DEBUG=true` temporarily in `.env` to see errors
- Check `storage/logs/laravel.log`
- Verify document root points to `public` folder

## Post-Deployment

1. Test the application thoroughly
2. Set `APP_DEBUG=false` in production
3. Set up SSL certificate (free Let's Encrypt available in cPanel)
4. Configure cron jobs if needed:
   ```
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong database password
- [ ] `.env` file is not publicly accessible
- [ ] SSL certificate installed
- [ ] Regular backups configured
- [ ] File permissions properly set
