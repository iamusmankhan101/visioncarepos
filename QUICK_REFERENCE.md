# Sales Import/Export - Quick Reference Card

## 🚨 MOST IMPORTANT

**Before importing sales, you MUST create products with matching SKUs!**

---

## 🔗 Quick Links

| Purpose | URL | What It Shows |
|---------|-----|---------------|
| Debug sales import | `/debug-sales-import` | Products, SKUs, import logs, why rows skipped |
| Test export route | `/sells/test-export` | Verify export route is working |
| Import sales | `/import-sales` | Upload and import sales |
| Sales page | `/sells` | View all sales |

---

## 📤 Export Sales

1. Go to Sales page
2. Select sales (optional)
3. Click "Export (X selected)"
4. File downloads

**Button text:**
- "Export (All)" - nothing selected
- "Export (5 selected)" - 5 sales selected

---

## 📥 Import Sales

### Before Import Checklist
- [ ] Products exist in system
- [ ] Product SKUs match Excel file
- [ ] Unit prices > 0 in Excel
- [ ] Business location available

### Import Steps
1. Go to Import Sales page
2. Upload Excel file
3. Map columns
4. Select location
5. Click Import
6. **Read success message!**

---

## 🐛 Troubleshooting

### Import says success but no sales?

**Visit**: `/debug-sales-import`

**Check**:
1. `sample_products` - Empty? Create products!
2. `sample_variations` - What SKUs exist?
3. `recent_import_logs` - Why skipped?

### Common Issues

| Issue | Cause | Solution |
|-------|-------|----------|
| No sales appear | Products don't exist | Create products with matching SKUs |
| Some rows skipped | SKU mismatch | Update Excel or product SKUs to match |
| All rows skipped | Unit price is 0 | Add prices in Excel |
| Can't see sales | Date filter | Change to "All time" |

---

## 📋 Template Format

**21 Columns** (in order):
1. Invoice No.
2. Customer Phone
3. Customer Name
4. Customer Email
5. Sale Date
6. Product Name
7. Product SKU ⚠️ **Must match database**
8. Quantity
9. Product Unit
10. Unit Price ⚠️ **Must be > 0**
11. Item Tax
12. Item Discount
13. Item Description
14. Order Total
15. Total Paid
16. Payment Method
17. Types of Service
18. Custom Field 1-4

---

## ✅ Success Messages

| Message | Meaning |
|---------|---------|
| "Sales imported successfully (5 sales imported)" | ✅ All good! |
| "...3 sales imported - 2 rows skipped" | ⚠️ Partial success, check logs |
| "Import completed but no sales were created" | ❌ All skipped, check debug page |

---

## 🔧 Quick Fixes

### Fix 1: Create Products
```
1. Go to Products menu
2. Add Product
3. Set SKU to match Excel (e.g., "SKU001")
4. Save
5. Re-import
```

### Fix 2: Check SKUs
```
1. Visit /debug-sales-import
2. Look at sample_variations
3. Compare with Excel file
4. Update Excel or products to match
```

### Fix 3: Add Prices
```
1. Open Excel file
2. Check "Unit Price" column
3. Ensure all values > 0
4. Save and re-import
```

---

## 📞 Need Help?

1. Visit `/debug-sales-import`
2. Share the JSON output
3. Check `recent_import_logs` section
4. Read documentation files

---

## 📚 Documentation Files

- `COMPLETE_SALES_IMPORT_EXPORT_SOLUTION.md` - Full overview
- `SALES_IMPORT_FIX_SUMMARY.md` - Detailed fixes
- `SALES_IMPORT_TROUBLESHOOTING.md` - Quick troubleshooting
- `SALES_IMPORT_EXPORT_DEBUG_GUIDE.md` - Debug guide
- `SALES_EXPORT_IMPORT_FORMAT_VERIFICATION.md` - Format details

---

## 🎯 Asset Version: 757

Clear browser cache: **Ctrl+Shift+R** (Windows) or **Cmd+Shift+R** (Mac)

---

**Remember: Products must exist BEFORE importing sales!** 🎯
