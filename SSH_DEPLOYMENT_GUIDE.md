# SSH Deployment Guide for Hostinger

## Prerequisites
1. SSH access enabled on your Hostinger account
2. Git repository set up on your Hostinger server
3. SSH key or password authentication configured

## Step-by-Step Deployment

### 1. Get Your SSH Details
From your Hostinger control panel, note:
- **SSH Host**: Usually your domain name or server IP
- **SSH Username**: Your hosting username
- **SSH Port**: Usually 22 (default)
- **Web Root Path**: Usually `/domains/yourdomain.com/public_html`

### 2. Connect via SSH
```bash
ssh your-username@your-domain.com
```

### 3. Navigate to Your Web Directory
```bash
cd /domains/your-domain.com/public_html
```

### 4. Pull Latest Changes
```bash
git pull origin main
```

### 5. Update Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 6. Run Database Migrations
```bash
php artisan migrate --force
```

### 7. Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 8. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 9. Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs storage/framework storage/app
```

## Quick One-Line Deployment
Once connected via SSH, you can run all commands at once:

```bash
cd /domains/your-domain.com/public_html && git pull origin main && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && chmod -R 755 storage bootstrap/cache
```

## Automated Script Usage

### Option 1: Use the automated script
1. Edit `deploy-ssh-simple.sh`
2. Replace the configuration variables with your actual details
3. Run: `./deploy-ssh-simple.sh`

### Option 2: Manual SSH commands
1. Connect to your server via SSH
2. Run the deployment commands manually
3. Verify the deployment was successful

## Troubleshooting

### Common Issues:
1. **Permission Denied**: Check SSH credentials and server access
2. **Git Pull Fails**: Ensure git repository is properly configured
3. **Composer Errors**: Check PHP version and memory limits
4. **Migration Errors**: Verify database connection and permissions

### Verification Steps:
1. Visit your website to ensure it loads
2. Check error logs: `tail -f storage/logs/laravel.log`
3. Test the related customer functionality
4. Verify all recent fixes are working

## What This Deployment Includes:
- ✅ Related customer creation and editing fixes
- ✅ Form validation error resolutions
- ✅ Modal handling improvements
- ✅ AJAX form submission fixes
- ✅ White screen issue resolution
- ✅ Prescription table functionality
- ✅ POS related customer selection