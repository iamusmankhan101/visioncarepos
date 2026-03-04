# Fix: Column 'assigned_customer_id' Not Found Error

## 🔴 Error Message
```
Error: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'assigned_customer_id' in 'WHERE'
```

## 🎯 Root Cause
The `assigned_customer_id` column doesn't exist in the `transaction_sell_lines` table yet. The migration needs to be run to create this column.

## ✅ Solution

### Option 1: Run Migration (Recommended)
If you have SSH access to your server:

```bash
# Navigate to your project directory
cd /home/u102957485/domains/digitrot.com/public_html/pos

# Run the migration
php artisan migrate

# Clear cache
php artisan cache:clear
```

### Option 2: Run SQL Script Directly
If you don't have SSH access, use phpMyAdmin or your database management tool:

1. **Open phpMyAdmin** or your database tool
2. **Select your database**
3. **Go to SQL tab**
4. **Copy and paste this SQL:**

```sql
-- Add assigned_customer_id column
ALTER TABLE `transaction_sell_lines` 
ADD COLUMN `assigned_customer_id` INT(10) UNSIGNED NULL 
AFTER `res_service_staff_id`;

-- Add foreign key constraint
ALTER TABLE `transaction_sell_lines` 
ADD CONSTRAINT `transaction_sell_lines_assigned_customer_id_foreign` 
FOREIGN KEY (`assigned_customer_id`) 
REFERENCES `contacts` (`id`) 
ON DELETE SET NULL;

-- Add index for performance
ALTER TABLE `transaction_sell_lines` 
ADD INDEX `transaction_sell_lines_assigned_customer_id_index` (`assigned_customer_id`);
```

5. **Click "Go" to execute**

### Option 3: Use Provided SQL File
I've created a ready-to-use SQL file:

1. Open `add_assigned_customer_column.sql`
2. Copy all the SQL
3. Run it in phpMyAdmin or your database tool

## 🔍 Verify the Fix

After running the migration or SQL, verify the column exists:

```sql
SHOW COLUMNS FROM `transaction_sell_lines` LIKE 'assigned_customer_id';
```

You should see:
```
Field: assigned_customer_id
Type: int(10) unsigned
Null: YES
Key: MUL
Default: NULL
Extra: 
```

## 🧪 Test the Fix

1. **Refresh your POS page** (Ctrl+F5)
2. **Select a customer**
3. **Add a product**
4. **Check if the Customer column appears**
5. **Try to complete a transaction**

If no errors appear, the fix is successful! ✅

## 🐛 Troubleshooting

### Error: "Table doesn't exist"
- Make sure you're connected to the correct database
- Check the table name is `transaction_sell_lines` (with underscore)

### Error: "Foreign key constraint fails"
- Make sure the `contacts` table exists
- Verify the `contacts` table has an `id` column

### Error: "Duplicate column name"
- The column already exists
- Run this to check: `SHOW COLUMNS FROM transaction_sell_lines;`
- If it exists, just refresh your page

### Still Getting Errors?
1. Clear browser cache (Ctrl+Shift+Delete)
2. Clear Laravel cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```
3. Check server error logs
4. Verify the migration file exists in `database/migrations/`

## 📋 Migration File Location

The migration file should be at:
```
database/migrations/2026_03_04_000001_add_assigned_customer_to_transaction_sell_lines.php
```

If it's missing, you can recreate it or use the SQL script instead.

## 🔄 Rollback (If Needed)

If you need to remove the column:

```sql
-- Remove foreign key
ALTER TABLE `transaction_sell_lines` 
DROP FOREIGN KEY `transaction_sell_lines_assigned_customer_id_foreign`;

-- Remove index
ALTER TABLE `transaction_sell_lines` 
DROP INDEX `transaction_sell_lines_assigned_customer_id_index`;

-- Remove column
ALTER TABLE `transaction_sell_lines` 
DROP COLUMN `assigned_customer_id`;
```

Or use the provided `remove_assigned_customer_column.sql` file.

## 📊 Database Structure After Fix

Your `transaction_sell_lines` table should have these columns:
```
- id
- transaction_id
- product_id
- variation_id
- quantity
- unit_price
- ...
- res_service_staff_id
- assigned_customer_id  ← NEW COLUMN
- created_at
- updated_at
```

## ✅ Success Indicators

You'll know the fix worked when:
1. ✅ No SQL errors in the browser
2. ✅ Customer column appears in POS product table
3. ✅ Customer dropdown is populated
4. ✅ You can complete transactions without errors
5. ✅ Products are assigned to customers successfully

## 🚀 Next Steps After Fix

1. **Test with a sample transaction**
2. **Verify invoices are generated correctly**
3. **Check that products are assigned to the right customers**
4. **Train staff on the new feature**

## 📞 Need Help?

If you're still having issues:
1. Check the error logs: `storage/logs/laravel.log`
2. Verify database connection settings in `.env`
3. Make sure you have database permissions to ALTER tables
4. Contact your hosting provider if you can't run migrations

## 🎯 Quick Command Reference

```bash
# Run migration
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback --step=1

# Clear all caches
php artisan cache:clear && php artisan config:clear && php artisan view:clear
```

---

**Priority:** HIGH 🔴  
**Estimated Fix Time:** 2-5 minutes  
**Difficulty:** Easy  
**Required Access:** Database access (phpMyAdmin or SSH)
