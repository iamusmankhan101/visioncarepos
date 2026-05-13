# Sales Import Troubleshooting - Quick Guide

## Step 1: Visit Debug Page

Go to: `http://your-domain.com/debug-sales-import`

This will show you:
- ✅ If imports were saved (check `last_imports`)
- ✅ If products exist (check `sample_products`)
- ✅ Why rows were skipped (check `recent_import_logs`)

## Step 2: Check Common Issues

### Issue 1: Products Don't Exist
**Symptom**: Import says "successful" but no sales appear

**Check**: Look at `sample_products` in debug output
- If empty → **You must create products first**
- If has products → Check if SKUs match your Excel file

**Solution**:
1. Go to Products menu
2. Create products with SKUs that match your import file
3. Or update your Excel file to match existing product SKUs

### Issue 2: Unit Prices Are 0 or Empty
**Symptom**: Import skips all rows

**Check**: Open your Excel file and verify "Unit Price" column has values > 0

**Solution**: Add unit prices to all rows in Excel file

### Issue 3: Product SKUs Don't Match
**Symptom**: Import logs show "Product not found"

**Check**: Compare Excel SKUs with `sample_variations` in debug output

**Solution**: 
- Update Excel file SKUs to match database
- Or update product SKUs in database to match Excel

### Issue 4: Date Filter Hiding Sales
**Symptom**: Import successful but sales don't appear in table

**Check**: Sales page date filter (top of page)

**Solution**: Change date filter to "All time" or wider date range

## Step 3: Check Laravel Logs

The import logs detailed information. Check `recent_import_logs` in debug output for:

```
Sales Import Summary
- imported_count: 0 (if 0, nothing was imported)
- skipped_count: X (number of rows skipped)
- skipped_details: [reasons why rows were skipped]
```

Common skip reasons:
- "Product not found - SKU: XXX"
- "No unit price"

## Step 4: Test With Sample Data

Use the template's sample row:
1. Download template from import page
2. Template has sample data in row 2
3. Create a product with SKU "SKU-001" (or whatever is in template)
4. Import the template as-is
5. Check if that one sale appears

## Quick Checklist

Before importing, ensure:
- [ ] Products exist in system
- [ ] Product SKUs match Excel file
- [ ] Unit prices are > 0 in Excel
- [ ] Business location is selected during import
- [ ] You're logged in with proper permissions

## Need More Help?

Share the output from `/debug-sales-import` to diagnose the exact issue.
