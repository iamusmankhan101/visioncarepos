# 🔧 Hostinger SSH Cache Clear - Correct Commands

## Your Directory Structure
Based on your SSH session, your structure is:
```
~/domains/digitrot.com/public_html/
```

## ✅ Correct Commands to Run

### Step 1: Navigate to Application Root
```bash
cd ~/domains/digitrot.com/public_html
```

### Step 2: Clear All Caches (One Command)
```bash
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan clear-compiled && rm -f bootstrap/cache/*.php && echo "✅ Done!"
```

### Or Step-by-Step:
```bash
# Make sure you're in the right directory
cd ~/domains/digitrot.com/public_html
pwd  # Should show: /home/u102957485/domains/digitrot.com/public_html

# Clear configuration cache (THIS IS THE MOST IMPORTANT ONE)
php artisan config:clear

# Clear application cache
php artisan cache:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear compiled files
php artisan clear-compiled

# Remove bootstrap cache files
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php

echo "✅ All caches cleared!"
```

## If PHP Artisan Doesn't Work

Try with full PHP path:
```bash
cd ~/domains/digitrot.com/public_html
/usr/bin/php artisan config:clear
/usr/bin/php artisan cache:clear
/usr/bin/php artisan route:clear
/usr/bin/php artisan view:clear
```

## Quick Test After Clearing
```bash
# Test database connection
php artisan tinker --execute="echo DB::connection()->getDatabaseName();"
```

## Alternative: Use the Web Script
If SSH commands don't work, just visit:
```
https://pos.digitrot.com/emergency_cache_clear.php
```

This will clear all caches through the web interface.
