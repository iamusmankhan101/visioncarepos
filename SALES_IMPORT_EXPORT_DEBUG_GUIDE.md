# Sales Import & Export Debugging Guide

## Current Status

### Issues
1. **Sales Import**: Shows "successful" message but sales don't appear in the sales table
2. **Sales Export**: Downloads file but it's empty

### Changes Made (Asset Version: 755)

#### 1. Enhanced Sales Export Logging
- Added detailed logging to track what's being exported
- Changed query to only export finalized sales (`status = 'final'`)
- Changed type filter from `['sell', 'pos']` to just `'sell'`
- Logs now show:
  - Business ID
  - Selected IDs (if any)
  - Number of transactions found
  - Transaction IDs being exported

#### 2. Improved Debug Route
- Enhanced `/debug-sales-import` route with more information
- Now shows:
  - Last 10 imported sales (with import_batch)
  - Last 10 recent sales (all sales)
  - Sample products (10)
  - Sample variations (10)
  - Sample contacts (5)
  - Business locations
  - Recent import logs from Laravel log file
  - Helpful instructions

## How to Debug

### Step 1: Check Debug Page
Visit: `http://your-domain.com/debug-sales-import`

This will show you:
- **last_imports**: Sales that were imported (have import_batch number)
- **recent_sales**: All recent sales in the system
- **sample_products**: Products available for import
- **sample_variations**: Product variations with SKUs
- **sample_contacts**: Customer contacts
- **locations**: Business locations
- **recent_import_logs**: Filtered log entries about imports

### Step 2: Interpret Results

#### If `last_imports` is empty:
- Imports are not being saved to database
- Check if products exist (see sample_products)
- Check if unit prices are > 0 in your Excel file

#### If `recent_sales` is empty:
- No sales exist in the system at all
- This is normal for a new system

#### If `sample_products` is empty:
- **YOU MUST CREATE PRODUCTS FIRST** before importing sales
- Go to Products menu and add products
- Make sure each product has a SKU or name that matches your import file

#### If `sample_variations` is empty:
- Products exist but have no variations
- Each product needs at least one variation
- Check product setup

### Step 3: Check Import Requirements

For sales import to work, you need:

1. **Products must exist** with matching:
   - SKU (in variation.sub_sku column) OR
   - Product name (exact match)

2. **Unit prices must be > 0**
   - Empty or zero prices will skip the row

3. **Business location must be selected**
   - Check `locations` in debug output

4. **Excel file format must match template**
   - Download template from import page
   - Column order matters during mapping

### Step 4: Check Laravel Logs

The import process logs detailed information:

```
Sales Import Summary
- imported_count: X
- skipped_count: Y
- skipped_details: [array of reasons]
```

Logs show:
- Which products were not found (by SKU or name)
- Which rows had no unit price
- How many sales were actually imported vs skipped

### Step 5: Test Sales Export

1. Go to Sales page
2. Select one or more sales (check the checkbox)
3. Click "Export (X selected)" button
4. Check browser console for errors
5. Check Laravel logs for export messages

The export will log:
```
Sales Export - Starting
Sales Export - Filtering by IDs (if selected)
Sales Export - Found transactions (count and IDs)
```

## Common Issues & Solutions

### Issue: Import shows success but no sales appear

**Possible Causes:**
1. All rows were skipped (products not found or no unit price)
2. Date filter on sales page is hiding imported sales
3. Wrong business location selected

**Solutions:**
1. Check `/debug-sales-import` for products
2. Create products with matching SKUs first
3. Clear date filter on sales page (set to "All time")
4. Check Laravel logs for "Sales Import Summary"

### Issue: Export file is empty

**Possible Causes:**
1. No sales exist with `type = 'sell'` and `status = 'final'`
2. Selected IDs don't match any transactions
3. Business ID mismatch

**Solutions:**
1. Check `/debug-sales-import` for recent_sales
2. Try exporting without selecting any (export all)
3. Check browser console for JavaScript errors
4. Check Laravel logs for export messages

### Issue: Products not found during import

**Solution:**
1. Go to Products menu
2. Add products with SKUs that match your Excel file
3. Or update Excel file to match existing product names/SKUs
4. Product names must match exactly (case-sensitive)

## Import Template Format

✅ **VERIFIED**: The export format matches the import template exactly (21 columns).

The import expects these columns (in order):
1. Invoice No.
2. Customer Phone number
3. Customer name
4. Customer Email
5. Sale Date
6. Product name
7. Product SKU
8. Quantity
9. Product Unit
10. Unit Price
11. Item Tax
12. Item Discount
13. Item Description
14. Order Total
15. Total Paid
16. Payment Method
17. Types of service
18. Custom Field 1
19. Custom Field 2
20. Custom Field 3
21. Custom Field 4

**Important:**
- Either Product name OR Product SKU must match existing products
- Unit Price must be > 0
- Quantity defaults to 1 if empty
- Customer will be created if doesn't exist (by phone or email)
- **Exported files can be directly re-imported without modifications**

See `SALES_EXPORT_IMPORT_FORMAT_VERIFICATION.md` for detailed format verification.

## Next Steps

1. Visit `/debug-sales-import` and share the output
2. Check if products exist
3. If no products, create them first
4. Try importing a single sale with a product that exists
5. Check Laravel logs at `storage/logs/laravel.log` for "Sales Import Summary"
6. Share any error messages from logs

## Files Modified

1. `app/Http/Controllers/SellController.php` - Enhanced export with logging
2. `routes/web.php` - Enhanced debug route
3. `config/constants.php` - Incremented asset_version to 755

## Cache Clearing

After these changes, clear your browser cache or hard refresh (Ctrl+Shift+R or Cmd+Shift+R).
