# Quick Reference: Product-Customer Assignment

## 🚀 Quick Start

### Installation (One-time)
```bash
php artisan migrate
php artisan cache:clear
```

### Usage (Every transaction)
1. Select customer
2. Add products
3. Assign each product to a customer
4. Complete sale

## 📋 Key Files

| File | Purpose |
|------|---------|
| `public/js/pos_product_customer_assignment.js` | JavaScript logic |
| `database/migrations/2026_03_04_000001_*.php` | Database schema |
| `app/Utils/TransactionUtil.php` | Backend processing |
| `resources/views/sale_pos/product_row.blade.php` | Product row template |

## 🎯 Common Tasks

### Check if Feature is Active
```javascript
// In browser console
console.log(window.posSelectedCustomers);
```

### Manually Validate Assignments
```javascript
window.validateProductCustomerAssignments();
```

### Get Current Assignments
```javascript
var assignments = window.getProductCustomerAssignments();
console.log(assignments);
```

### Clear All Selections
```javascript
window.clearCustomerSelections();
```

## 🐛 Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| Column not showing | Clear browser cache (Ctrl+F5) |
| Dropdown empty | Check console for errors |
| Not saving | Check migration ran successfully |
| Validation failing | Ensure all products assigned |

## 📊 Database Queries

### Check Column Exists
```sql
SHOW COLUMNS FROM transaction_sell_lines LIKE 'assigned_customer_id';
```

### View Assignments
```sql
SELECT tsl.id, p.name as product, c.name as customer
FROM transaction_sell_lines tsl
LEFT JOIN products p ON tsl.product_id = p.id
LEFT JOIN contacts c ON tsl.assigned_customer_id = c.id
WHERE tsl.assigned_customer_id IS NOT NULL
LIMIT 10;
```

### Count Assignments
```sql
SELECT COUNT(*) as total_assignments
FROM transaction_sell_lines
WHERE assigned_customer_id IS NOT NULL;
```

## 🔍 Debugging

### Browser Console
```javascript
// Check if module loaded
console.log('Module loaded:', typeof window.posSelectedCustomers !== 'undefined');

// Check selected customers
console.log('Selected customers:', window.posSelectedCustomers);

// Check assignments
console.log('Assignments:', window.getProductCustomerAssignments());

// Test validation
console.log('Valid:', window.validateProductCustomerAssignments());
```

### PHP Debugging
```php
// In TransactionUtil.php
\Log::info('Product assignments:', $product);
\Log::info('Assigned customer ID:', $product['assigned_customer_id'] ?? 'none');
```

## ⚙️ Configuration

### Make Assignment Required (Always)
Edit `public/js/pos_product_customer_assignment.js`:
```javascript
// Line ~180
if (window.posSelectedCustomers.length > 0) { // Changed from > 1
```

### Auto-assign to Primary Customer
Edit `public/js/pos_product_customer_assignment.js`:
```javascript
// In populateCustomerDropdownForRow()
if (!currentValue && window.posSelectedCustomers.length > 0) {
    $dropdown.val(window.posSelectedCustomers[0].id);
}
```

### Hide Customer Column
Edit `resources/views/sale_pos/partials/pos_form.blade.php`:
```php
<th class="hide">@lang('sale.customer')</th>
```

## 📱 Mobile Considerations

The feature works on mobile devices but:
- Dropdowns may be small on small screens
- Consider using select2 for better mobile UX
- Test thoroughly on target devices

## 🎨 Styling

### Change Column Width
Edit `resources/views/sale_pos/partials/pos_form.blade.php`:
```php
<th class="col-md-3">@lang('sale.customer')</th>
```

### Style Dropdown
Add to your CSS:
```css
.product_customer_assignment {
    font-size: 14px;
    padding: 5px;
}
```

## 📈 Performance Tips

1. **Use indexes** - Already added on `assigned_customer_id`
2. **Eager load** - Already using `with()` for relationships
3. **Cache customers** - Consider caching frequently used customers
4. **Limit products** - For very large transactions, consider pagination

## 🔐 Security Notes

- ✅ Foreign key constraints prevent invalid customer IDs
- ✅ Eloquent ORM prevents SQL injection
- ✅ Form validation prevents invalid submissions
- ✅ Customer IDs validated against contacts table

## 📞 Support Contacts

| Issue Type | Action |
|------------|--------|
| JavaScript errors | Check browser console |
| PHP errors | Check `storage/logs/laravel.log` |
| Database errors | Check migration status |
| Feature not working | Review setup guide |

## ✅ Success Checklist

- [ ] Migration completed
- [ ] Customer column visible
- [ ] Dropdown populates
- [ ] Products can be assigned
- [ ] Validation works
- [ ] Data saves correctly
- [ ] Invoices show correct products
- [ ] No console errors
- [ ] No server errors

## 🎓 Training Tips

### For Staff
1. Show them the new Customer column
2. Demonstrate assigning products
3. Explain validation messages
4. Show sample invoices

### For Managers
1. Explain business benefits
2. Show reporting capabilities
3. Demonstrate audit trail
4. Discuss use cases

## 📚 Documentation Links

- **Full Documentation:** `PRODUCT_CUSTOMER_ASSIGNMENT_FEATURE.md`
- **Setup Guide:** `SETUP_PRODUCT_CUSTOMER_ASSIGNMENT.md`
- **Implementation Details:** `IMPLEMENTATION_SUMMARY.md`

## 🎯 Best Practices

1. **Always assign products** when multiple customers are involved
2. **Verify assignments** before completing the sale
3. **Test invoices** to ensure correct product distribution
4. **Train staff** on the new feature
5. **Monitor logs** for any issues

## 💡 Tips & Tricks

- **Keyboard shortcuts:** Use Tab to navigate between dropdowns
- **Quick assign:** Select customer before adding products for auto-assignment
- **Bulk operations:** Add all products first, then assign in batch
- **Verification:** Review assignments before payment

## 🔄 Workflow

```
1. Select Customer → 2. Add Products → 3. Assign to Customers → 4. Validate → 5. Complete Sale
```

## 📊 Reporting

### Products by Customer
```sql
SELECT 
    c.name as customer,
    COUNT(tsl.id) as product_count,
    SUM(tsl.quantity) as total_quantity
FROM transaction_sell_lines tsl
JOIN contacts c ON tsl.assigned_customer_id = c.id
GROUP BY c.id, c.name
ORDER BY product_count DESC;
```

### Transactions with Multiple Customers
```sql
SELECT 
    t.id,
    t.invoice_no,
    COUNT(DISTINCT tsl.assigned_customer_id) as customer_count
FROM transactions t
JOIN transaction_sell_lines tsl ON t.id = tsl.transaction_id
WHERE tsl.assigned_customer_id IS NOT NULL
GROUP BY t.id, t.invoice_no
HAVING customer_count > 1;
```

---

**Version:** 1.0.0  
**Last Updated:** 2026-03-04  
**Status:** Production Ready ✅
