# 🚨 Quick Fix for 500 Error - Database Configuration Issue

## Problem Identified
Your production server has **cached configuration** pointing to SQLite database instead of MySQL. This is causing the error:
```
SQLSTATE[HY000] [14] unable to open database file
```

## Solution: Clear Production Server Cache

### Option 1: Use the Fix Script (Easiest)
1. Go to: `https://pos.digitrot.com/fix_500_database_error.php`
2. This will automatically clear all caches
3. Then try accessing your site again

### Option 2: SSH into Hostinger (Recommended)
If you have SSH access to your Hostinger server:

```bash
cd ~/public_html
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
rm -f bootstrap/cache/*.php
```

### Option 3: Manual File Deletion via File Manager
1. Login to Hostinger File Manager
2. Navigate to your application root
3. Delete these files:
   - `bootstrap/cache/config.php`
   - `bootstrap/cache/routes.php`
   - `bootstrap/cache/services.php`
   - `bootstrap/cache/packages.php`

### Option 4: Create a Simple Cache Clear Script
Access: `https://pos.digitrot.com/emergency_cache_clear.php`

## Why This Happened
- Your `.env` file is correctly configured for MySQL
- But Laravel cached the old SQLite configuration
- The cache needs to be cleared to read the new `.env` settings

## After Clearing Cache
Your application should work normally. The `.env` file shows correct MySQL configuration:
- Database: u102957485_visioncare
- Host: 127.0.0.1
- Port: 3306

## Test After Fix
1. `https://pos.digitrot.com/home` - Dashboard
2. `https://pos.digitrot.com/pos/create` - POS System
3. `https://pos.digitrot.com/business/select` - Business Selection
