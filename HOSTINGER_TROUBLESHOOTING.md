# Hostinger Deployment Troubleshooting

## Error: "Your requirements could not be resolved to an installable set of packages"

This error occurs when Hostinger's deployment system can't install composer dependencies. Here are the solutions:

---

## Solution 1: Set Correct PHP Version in Hostinger

### Via cPanel:
1. Log into Hostinger cPanel
2. Go to **"Select PHP Version"** or **"PHP Configuration"**
3. Select **PHP 8.1** or **PHP 8.2** (required for this app)
4. Enable these extensions:
   - ✅ mbstring
   - ✅ openssl
   - ✅ pdo
   - ✅ pdo_mysql
   - ✅ tokenizer
   - ✅ xml
   - ✅ ctype
   - ✅ json
   - ✅ bcmath
   - ✅ fileinfo
   - ✅ gd
   - ✅ zip
   - ✅ curl
5. Click **Save**

### Via .htaccess (if cPanel method doesn't work):
Add this to your `.htaccess` file in the root directory:
```apache
# Force PHP 8.2
AddHandler application/x-httpd-php82 .php
```

---

## Solution 2: Manual Deployment (Recommended)

Instead of using Hostinger's Git deployment, deploy manually:

### Step 1: Upload Files
1. Download your project as ZIP from GitHub
2. Upload to Hostinger via FTP or File Manager
3. Extract in your domain directory (e.g., `public_html`)

### Step 2: SSH into Hostinger
```bash
ssh your-username@your-domain.com
```

### Step 3: Navigate to Your Project
```bash
cd public_html  # or your domain directory
```

### Step 4: Check PHP Version
```bash
php -v
```
Should show PHP 8.1 or higher. If not:
```bash
# Try these alternatives
php81 -v
php82 -v
php83 -v
```

### Step 5: Install Dependencies
If default `php` is 8.1+:
```bash
composer install --optimize-autoloader --no-dev
```

If you need to use specific PHP version:
```bash
php82 /usr/local/bin/composer install --optimize-autoloader --no-dev
```

Or if composer is not available globally:
```bash
php82 composer.phar install --optimize-autoloader --no-dev
```

### Step 6: Set Up Environment
```bash
cp .env.example .env
nano .env  # or use File Manager to edit
```

Edit these values:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### Step 7: Generate Key
```bash
php artisan key:generate
```

### Step 8: Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/framework
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache
```

### Step 9: Run Migrations
```bash
php artisan migrate --force
```

### Step 10: Optimize
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Solution 3: Fix Composer Lock File

The composer.lock might be out of sync. If you have SSH access:

```bash
# Remove composer.lock
rm composer.lock

# Regenerate it
composer update --lock

# Then install
composer install --optimize-autoloader --no-dev
```

---

## Solution 4: Use Platform Requirements

If Hostinger has PHP 8.1+ but still fails, try ignoring platform requirements temporarily:

```bash
composer install --optimize-autoloader --no-dev --ignore-platform-reqs
```

⚠️ **Warning**: Only use this if you're sure the server has the required PHP version and extensions.

---

## Solution 5: Contact Hostinger Support

If none of the above works:

1. Open a support ticket with Hostinger
2. Ask them to:
   - Enable PHP 8.1 or 8.2 for your account
   - Enable all required PHP extensions (list above)
   - Check if composer is available
   - Provide SSH access if not already enabled

---

## Alternative: Deploy Without Git Integration

### Option A: FTP Upload + Manual Setup
1. Upload files via FTP
2. SSH in and run commands manually (see Solution 2)

### Option B: Use Deployment Script
1. Upload files via FTP
2. Upload the `deploy-hostinger.sh` script
3. SSH in and run:
```bash
chmod +x deploy-hostinger.sh
./deploy-hostinger.sh
```

---

## Verify Installation

After successful deployment, check:

1. **PHP Version**:
   ```bash
   php -v
   ```

2. **Composer Version**:
   ```bash
   composer --version
   ```

3. **Laravel Version**:
   ```bash
   php artisan --version
   ```

4. **Required Extensions**:
   ```bash
   php -m
   ```

5. **Test Application**:
   - Visit: https://yourdomain.com
   - Should see login page or installation page

---

## Common Issues

### Issue: "Class not found"
**Solution**: Run `composer dump-autoload`

### Issue: "Permission denied"
**Solution**: Fix permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

### Issue: "500 Internal Server Error"
**Solution**: 
1. Check `storage/logs/laravel.log`
2. Enable debug temporarily: `APP_DEBUG=true` in `.env`
3. Check Apache error logs in cPanel

### Issue: "Database connection failed"
**Solution**: 
1. Verify database credentials in `.env`
2. Check if database exists in cPanel
3. Ensure database user has all privileges

### Issue: "Composer not found"
**Solution**: 
1. Download composer.phar:
   ```bash
   curl -sS https://getcomposer.org/installer | php
   ```
2. Use it:
   ```bash
   php composer.phar install --optimize-autoloader --no-dev
   ```

---

## Need Help?

If you're still stuck:
1. Check `storage/logs/laravel.log` for errors
2. Check Hostinger's error logs in cPanel
3. Contact Hostinger support with specific error messages
4. Share the exact error message for more specific help
