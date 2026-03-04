# 🚀 Clear Laravel Cache via SSH - Hostinger

## Quick Commands (Copy & Paste)

### Option 1: One-Line Command (Fastest)
```bash
cd ~/public_html && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan clear-compiled && rm -f bootstrap/cache/*.php && echo "✅ Cache cleared!"
```

### Option 2: Step-by-Step Commands
```bash
# Navigate to your application
cd ~/public_html

# Clear all Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled

# Remove bootstrap cache files
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php

# Clear storage cache
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/views/*.php

# Optimize autoloader
composer dump-autoload --optimize

echo "✅ All caches cleared!"
```

### Option 3: Upload and Run Script
1. Upload `clear-cache-ssh.sh` to your server
2. Make it executable:
   ```bash
   chmod +x clear-cache-ssh.sh
   ```
3. Run it:
   ```bash
   ./clear-cache-ssh.sh
   ```

## How to Connect via SSH

### Using PuTTY (Windows)
1. Open PuTTY
2. Host: Your Hostinger server IP or domain
3. Port: 22
4. Click "Open"
5. Login with your Hostinger SSH credentials

### Using Terminal (Mac/Linux) or PowerShell
```bash
ssh username@your-server-ip
# or
ssh username@pos.digitrot.com
```

### Using Hostinger's Built-in SSH
1. Login to Hostinger control panel
2. Go to "Advanced" → "SSH Access"
3. Click "Open SSH Terminal"
4. Paste the commands above

## What Each Command Does

| Command | Purpose |
|---------|---------|
| `php artisan config:clear` | Clears configuration cache (fixes DB connection issues) |
| `php artisan cache:clear` | Clears application cache |
| `php artisan route:clear` | Clears route cache |
| `php artisan view:clear` | Clears compiled Blade views |
| `php artisan clear-compiled` | Removes compiled class files |
| `rm -f bootstrap/cache/*.php` | Deletes bootstrap cache files |
| `composer dump-autoload` | Regenerates autoload files |

## After Clearing Cache

Test your application:
- Dashboard: https://pos.digitrot.com/home
- POS: https://pos.digitrot.com/pos/create
- Business Selection: https://pos.digitrot.com/business/select

## If Still Not Working

### Restart PHP-FPM
```bash
killall -9 php-fpm
```

### Check File Permissions
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Verify .env File
```bash
cat .env | grep DB_
```
Should show:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u102957485_visioncare
DB_USERNAME=u102957485_dbuser
```

## Troubleshooting

### Error: "php: command not found"
Try using full path:
```bash
/usr/bin/php artisan config:clear
```

### Error: "Permission denied"
Add sudo (if available):
```bash
sudo php artisan config:clear
```

Or fix permissions:
```bash
chmod -R 755 storage bootstrap/cache
```

### Error: "artisan: No such file or directory"
Make sure you're in the correct directory:
```bash
cd ~/public_html
pwd  # Should show your application root
ls -la artisan  # Should show the artisan file
```
