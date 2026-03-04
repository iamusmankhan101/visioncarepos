# Deploy Related Customers Fix to Hostinger

## Option 1: Via Git (If you're using Git)

```bash
# On your local machine
git add app/Http/Controllers/ContactController.php
git add resources/views/contact/edit.blade.php
git commit -m "Fix: Display related customers in edit form"
git push origin main

# SSH into Hostinger
ssh u102957485@pos.digitrot.com
cd domains/digitrot.com/public_html/pos

# Pull the changes
git pull origin main

# Clear caches
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Cache for production
php artisan view:cache
php artisan config:cache
```

## Option 2: Via File Manager (Manual Upload)

### Step 1: Upload Modified Files

1. **Log into Hostinger File Manager**
2. **Navigate to**: `domains/digitrot.com/public_html/pos/`

3. **Upload these files** (overwrite existing):
   - `app/Http/Controllers/ContactController.php`
   - `resources/views/contact/edit.blade.php`

### Step 2: Clear Caches via SSH

```bash
# SSH into Hostinger
ssh u102957485@pos.digitrot.com
cd domains/digitrot.com/public_html/pos

# Clear caches
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Cache for production
php artisan view:cache
php artisan config:cache
```

### Step 3: Test

1. Go to https://pos.digitrot.com
2. Navigate to Contacts → Customers
3. Edit a customer that has related customers
4. Scroll down - you should see the "Related Customers" section

## Option 3: Via FTP

### Step 1: Connect via FTP

1. **Use an FTP client** (FileZilla, WinSCP, etc.)
2. **Connect to**:
   - Host: `ftp.digitrot.com` or your Hostinger FTP host
   - Username: `u102957485`
   - Password: Your FTP password
   - Port: 21

### Step 2: Upload Files

1. Navigate to: `/domains/digitrot.com/public_html/pos/`
2. Upload:
   - `app/Http/Controllers/ContactController.php`
   - `resources/views/contact/edit.blade.php`

### Step 3: Clear Caches

Follow Step 2 from Option 2 above.

## Verification

After deployment, verify the fix works:

1. **Create a test customer with a related customer:**
   - Add Customer → Fill details → Click "Add Another Customer"
   - Fill related customer details → Save

2. **Edit the first customer:**
   - Should see "Related Customers" section at the bottom
   - Table should show the related customer's info

3. **Check for errors:**
   - Open browser console (F12)
   - Look for any JavaScript errors
   - Check `storage/logs/laravel.log` for PHP errors

## Rollback (If Needed)

If something goes wrong, you can rollback:

### Via Git:
```bash
git revert HEAD
git push origin main
# Then pull on server
```

### Via File Manager:
- Re-upload the original files from backup

## Troubleshooting

### Issue: Changes not showing
**Solution:**
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```
Then hard refresh browser: `Ctrl + Shift + R`

### Issue: 500 Error after deployment
**Solution:**
```bash
# Check logs
tail -50 storage/logs/laravel.log

# Check syntax
php artisan route:list
```

### Issue: Related customers not showing
**Possible causes:**
1. No related customers exist in database
2. Custom fields don't contain negative IDs
3. Cache not cleared

**Check database:**
```sql
SELECT id, name, custom_field1, custom_field2 
FROM contacts 
WHERE business_id = 1 
LIMIT 10;
```

---

**Ready to deploy!** Choose the option that works best for you.
