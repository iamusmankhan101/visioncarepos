# Quick Setup Guide: Product-Customer Assignment Feature

## 🚀 Installation Steps

### Step 1: Run Database Migration
```bash
php artisan migrate
```

This will add the `assigned_customer_id` column to the `transaction_sell_lines` table.

### Step 2: Clear Cache (Optional but Recommended)
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Step 3: Verify Files
Ensure these files exist and are properly uploaded:
- ✅ `public/js/pos_product_customer_assignment.js`
- ✅ `database/migrations/2026_03_04_000001_add_assigned_customer_to_transaction_sell_lines.php`
- ✅ Modified: `resources/views/sale_pos/partials/pos_form.blade.php`
- ✅ Modified: `resources/views/sale_pos/product_row.blade.php`
- ✅ Modified: `resources/views/sale_pos/create.blade.php`
- ✅ Modified: `app/Utils/TransactionUtil.php`

### Step 4: Test the Feature

1. **Go to POS Screen**
   - Navigate to: `/pos/create`

2. **Select a Customer**
   - Choose any customer from the dropdown

3. **Add Products**
   - Add 2-3 products to the cart

4. **Check Customer Column**
   - You should see a new "Customer" column in the product table
   - Each product row should have a customer dropdown

5. **Assign Products**
   - Select customers for each product
   - If only one customer is selected, it should auto-assign

6. **Complete Transaction**
   - Process the payment
   - Generate invoice

## 🎯 How to Use

### Single Customer (Default Behavior)
- Select one customer
- Add products
- Products are automatically assigned to that customer
- Works exactly as before

### Multiple Customers
1. Select primary customer
2. Add products to cart
3. For each product, select which customer it's for
4. Complete the sale
5. Each customer gets an invoice with only their products

## 🔍 Verification Checklist

- [ ] Migration ran successfully
- [ ] New column exists in database
- [ ] Customer column appears in POS product table
- [ ] Customer dropdown appears in each product row
- [ ] Dropdown populates with selected customers
- [ ] Products can be assigned to customers
- [ ] Form validates assignments before submission
- [ ] Invoices show correct products per customer

## 🐛 Troubleshooting

### Issue: Customer column not showing
**Solution:**
1. Clear browser cache (Ctrl+F5)
2. Check if `pos_form.blade.php` was updated
3. Verify file permissions

### Issue: Customer dropdown is empty
**Solution:**
1. Open browser console (F12)
2. Check for JavaScript errors
3. Verify `pos_product_customer_assignment.js` is loaded
4. Type in console: `console.log(window.posSelectedCustomers)`

### Issue: Migration fails
**Solution:**
1. Check if column already exists:
   ```sql
   DESCRIBE transaction_sell_lines;
   ```
2. If column exists, skip migration or modify it
3. Check database user permissions

### Issue: Assignments not saving
**Solution:**
1. Check browser console for errors
2. Check server logs: `storage/logs/laravel.log`
3. Verify `TransactionUtil.php` was updated correctly
4. Test with a simple transaction

## 📊 Database Verification

Check if the migration was successful:

```sql
-- Check if column exists
SHOW COLUMNS FROM transaction_sell_lines LIKE 'assigned_customer_id';

-- Check foreign key
SHOW CREATE TABLE transaction_sell_lines;

-- Test query
SELECT id, product_id, assigned_customer_id 
FROM transaction_sell_lines 
WHERE assigned_customer_id IS NOT NULL 
LIMIT 10;
```

## 🎨 Customization

### Change Column Label
Edit `resources/views/sale_pos/partials/pos_form.blade.php`:
```php
<th>@lang('sale.customer')</th>
```

### Make Assignment Required
Edit `public/js/pos_product_customer_assignment.js`:
```javascript
// Change this line to always validate
if (window.posSelectedCustomers.length > 0) { // Changed from > 1
    if (!window.validateProductCustomerAssignments()) {
        e.preventDefault();
        return false;
    }
}
```

### Auto-assign to Primary Customer
Edit `public/js/pos_product_customer_assignment.js`:
```javascript
// In populateCustomerDropdownForRow function
// Auto-select primary customer if no assignment
if (!currentValue && window.posSelectedCustomers.length > 0) {
    $dropdown.val(window.posSelectedCustomers[0].id);
}
```

## 📝 Notes

- This feature is backward compatible
- Existing transactions are not affected
- The feature only activates when multiple customers are involved
- Single customer transactions work as before

## ✅ Success Indicators

You'll know the feature is working when:
1. ✅ Customer column appears in POS product table
2. ✅ Dropdown shows selected customers
3. ✅ Products can be assigned to different customers
4. ✅ Validation prevents unassigned products (with multiple customers)
5. ✅ Invoices show correct products per customer
6. ✅ No JavaScript errors in console
7. ✅ No PHP errors in logs

## 🆘 Need Help?

If you encounter issues:
1. Check browser console (F12) for JavaScript errors
2. Check `storage/logs/laravel.log` for PHP errors
3. Review `PRODUCT_CUSTOMER_ASSIGNMENT_FEATURE.md` for detailed documentation
4. Verify all files were uploaded correctly
5. Ensure migration was run successfully

## 🎉 You're Done!

The feature is now ready to use. Test it with a sample transaction to ensure everything works as expected.
