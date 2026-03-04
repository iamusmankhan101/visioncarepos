# Fix 500 Internal Server Error on Hostinger

Your deployment succeeded, but the site is showing a 500 error. Here's how to fix it:

---

## Quick Fix (Run These Commands via SSH)

```bash
# SSH into your server
ssh your-username@pos.digitrot.com

# Navigate to your project directory
cd public_html  # or wherever your files are

# Run the debug script
chmod +x debug-hostinger.sh
./debug-hostinger.sh
```

The script will automatically:
- Check if .env exists (create if missing)
- Generate APP_KEY if missing
- Fix file permissions
- Clear all caches
- Show you any errors

---

## Manual Fix (If Script Doesn't Work)

### Step 1: Check .env File
```bash
# Check if .env exists
ls -la .env

# If not, create it
cp .env.example .env
```

### Step 2: Generate Application Key
```bash
php artisan key:generate
```

### Step 3: Configure Database
Edit `.env` file:
```bash
nano .env
```

Update these lines:
```env
APP_ENV=production
APP_DEBUG=false  # Set to true temporarily to see errors
APP_URL=https://pos.digitrot.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Save and exit (Ctrl+X, then Y, then Enter)

### Step 4: Fix Permissions
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/framework
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache
```

### Step 5: Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 6: Run Migrations
```bash
php artisan migrate --force
```

### Step 7: Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Check Document Root

The document root MUST point to the `public` folder:

### Via cPanel:
1. Go to **Domains** → **Manage**
2. Find your domain: `pos.digitrot.com`
3. Click **Manage**
4. Change **Document Root** to: `/public_html/public` (or wherever your public folder is)
5. Save

### Via .htaccess (Alternative):
If you can't change document root, add this to `.htaccess` in root:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## Enable Debug Mode (Temporarily)

To see the actual error:

1. Edit `.env`:
   ```bash
   nano .env
   ```

2. Change:
   ```env
   APP_DEBUG=true
   ```

3. Clear config:
   ```bash
   php artisan config:clear
   ```

4. Visit: https://pos.digitrot.com

5. You'll see the actual error message

6. **IMPORTANT**: After fixing, set back to:
   ```env
   APP_DEBUG=false
   ```

---

## Check Laravel Logs

```bash
# View last 50 lines of log
tail -50 storage/logs/laravel.log

# Or view entire log
cat storage/logs/laravel.log
```

---

## Common 500 Error Causes

### 1. Missing APP_KEY
**Error**: "No application encryption key has been specified"
**Fix**: `php artisan key:generate`

### 2. Wrong Permissions
**Error**: "The stream or file could not be opened"
**Fix**: `chmod -R 775 storage bootstrap/cache`

### 3. Database Connection Failed
**Error**: "SQLSTATE[HY000] [1045] Access denied"
**Fix**: Check database credentials in `.env`

### 4. Wrong Document Root
**Error**: 500 with no specific message
**Fix**: Point document root to `/public` folder

### 5. Missing Vendor Directory
**Error**: "Class not found"
**Fix**: `composer install --optimize-autoloader --no-dev`

### 6. Cached Config with Wrong Values
**Error**: Various errors
**Fix**: `php artisan config:clear`

---

## Verification Checklist

After fixing, verify:

- [ ] `.env` file exists and has correct values
- [ ] `APP_KEY` is set in `.env`
- [ ] Database credentials are correct
- [ ] `storage` and `bootstrap/cache` are writable (775)
- [ ] `vendor` directory exists
- [ ] Document root points to `public` folder
- [ ] Migrations have run successfully
- [ ] Config is cached: `php artisan config:cache`

---

## Still Not Working?

1. **Check Apache/PHP Error Logs**:
   - cPanel → Errors
   - Look for PHP errors

2. **Check .htaccess in public folder**:
   ```bash
   cat public/.htaccess
   ```
   Should contain Laravel's default rewrite rules

3. **Test PHP**:
   Create `test.php` in public folder:
   ```php
   <?php
   phpinfo();
   ```
   Visit: https://pos.digitrot.com/test.php
   Check PHP version and extensions

4. **Contact Hostinger Support**:
   - Share the error from `storage/logs/laravel.log`
   - Ask them to check Apache error logs
   - Verify PHP 8.1+ is active

---

## Quick Command Reference

```bash
# Debug everything
./debug-hostinger.sh

# Generate key
php artisan key:generate

# Fix permissions
chmod -R 775 storage bootstrap/cache

# Clear all caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Cache for production
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Check logs
tail -50 storage/logs/laravel.log

# Test database connection
php artisan migrate:status
```
