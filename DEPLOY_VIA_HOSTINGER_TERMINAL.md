# Deploy Prescription Table via Hostinger Terminal

## Steps:

1. **Login to Hostinger**
   - Go to: https://hpanel.hostinger.com

2. **Open Terminal**
   - Go to: Advanced → Terminal (SSH Access)
   - Or use the built-in terminal in File Manager

3. **Run these commands:**

```bash
cd domains/digitrot.com/public_html/pos
git pull origin main
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

4. **Done!**
   - Visit: https://pos.digitrot.com
   - The prescription form will now show as a table

## What this does:
- Pulls the latest code from GitHub (including the table format)
- Clears all Laravel caches so changes take effect immediately

---

**Alternative: Manual File Upload**

If Git doesn't work, upload these 3 files via File Manager:
- `resources/views/contact/create.blade.php`
- `resources/views/contact/edit.blade.php`
- `resources/views/contact/contact_more_info.blade.php`

Upload to: `domains/digitrot.com/public_html/pos/resources/views/contact/`
