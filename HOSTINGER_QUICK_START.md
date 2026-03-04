# Hostinger Quick Start Checklist

## ⚠️ Your Deployment Failed Because:
The Hostinger Git deployment system couldn't install composer dependencies. This usually means:
- PHP version is below 8.1
- Required PHP extensions are missing
- Composer is not properly configured

---

## ✅ Quick Fix (Choose One Method)

### Method 1: Manual Deployment (Easiest)

1. **Download Project**
   - Go to: https://github.com/iamusmankhan101/visioncarepos
   - Click "Code" → "Download ZIP"

2. **Upload to Hostinger**
   - Log into Hostinger File Manager
   - Navigate to `public_html` (or your domain folder)
   - Upload and extract the ZIP file

3. **Set PHP Version**
   - cPanel → "Select PHP Version"
   - Choose **PHP 8.2** or **PHP 8.1**
   - Enable all extensions (mbstring, pdo, mysql, etc.)

4. **SSH into Hostinger**
   ```bash
   ssh your-username@your-domain.com
   cd public_html
   ```

5. **Run Fix Script**
   ```bash
   chmod +x fix-composer-hostinger.sh
   ./fix-composer-hostinger.sh
   ```

6. **Configure Environment**
   ```bash
   cp .env.example .env
   nano .env  # Edit database credentials
   ```

7. **Finish Setup**
   ```bash
   php artisan key:generate
   php artisan migrate --force
   chmod -R 775 storage bootstrap/cache
   ```

8. **Set Document Root**
   - cPanel → Domains → Manage
   - Change document root to: `/public_html/public`

---

### Method 2: Fix Git Deployment

1. **Set PHP Version First**
   - cPanel → "Select PHP Version" → PHP 8.2
   - Enable all extensions

2. **Try Git Deployment Again**
   - Hostinger → Git → Redeploy

3. **If Still Fails**
   - SSH into server
   - Navigate to deployment directory
   - Run: `./fix-composer-hostinger.sh`

---

## 📋 Required Information

Before starting, have these ready:

- [ ] Hostinger cPanel login
- [ ] SSH access credentials
- [ ] Database name (create in cPanel if needed)
- [ ] Database username
- [ ] Database password
- [ ] Your domain name

---

## 🔧 Create Database

1. cPanel → MySQL Databases
2. Create new database: `visioncarepos`
3. Create new user with strong password
4. Add user to database with ALL PRIVILEGES
5. Note down: database name, username, password

---

## 📝 Environment Configuration

Edit `.env` file with these values:

```env
APP_NAME="Vision Care POS"
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

---

## ✅ Verification

After deployment, check:

1. **Visit your domain**: Should see login page
2. **Check PHP version**: `php -v` (should be 8.1+)
3. **Check logs**: `storage/logs/laravel.log`
4. **Test login**: Use your admin credentials

---

## 🆘 Still Having Issues?

1. Read: `HOSTINGER_TROUBLESHOOTING.md`
2. Check: `storage/logs/laravel.log`
3. Contact Hostinger support and ask:
   - "Please enable PHP 8.2 for my account"
   - "Please enable all PHP extensions for Laravel"
   - "I need SSH access to install composer dependencies"

---

## 📞 Hostinger Support Info

- Live Chat: Available in cPanel
- Email: support@hostinger.com
- Phone: Check your Hostinger dashboard

Tell them: "I need PHP 8.2 with all extensions enabled for Laravel 9 deployment"
