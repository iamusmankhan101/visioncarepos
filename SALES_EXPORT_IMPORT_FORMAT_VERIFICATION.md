# Sales Export/Import Format Verification

## ✅ VERIFIED: Export and Import Formats Match Perfectly

The sales export feature generates files in **exactly the same format** as the import template. This means:

- You can export sales and re-import them without any modifications
- The column order is identical
- The data format is compatible
- All 21 columns match perfectly

## Column Mapping (1-to-1 Match)

| # | Column Name | Export Source | Import Mapping |
|---|-------------|---------------|----------------|
| 1 | Invoice No. | `transaction.invoice_no` | `invoice_no` |
| 2 | Customer Phone number | `contact.mobile` | `customer_phone_number` |
| 3 | Customer name | `contact.name` | `customer_name` |
| 4 | Customer Email | `contact.email` | `customer_email` |
| 5 | Sale Date | `transaction.transaction_date` | `date` |
| 6 | Product name | `product.name` | `product` |
| 7 | Product SKU | `variation.sub_sku` | `sku` |
| 8 | Quantity | `sell_line.quantity` | `quantity` |
| 9 | Product Unit | `unit.actual_name` or `unit.short_name` | `unit` |
| 10 | Unit Price | `sell_line.unit_price` | `unit_price` |
| 11 | Item Tax | `tax_rate.name` | `item_tax` |
| 12 | Item Discount | `sell_line.line_discount_amount` | `item_discount` |
| 13 | Item Description | `sell_line.sell_line_note` | `item_description` |
| 14 | Order Total | `transaction.final_total` | `order_total` |
| 15 | Total Paid | Sum of `payment_lines.amount` | `total_paid` |
| 16 | Payment Method | `payment_line.method` | `payment_method` |
| 17 | Types of service | `types_of_service.name` | `types_of_service` |
| 18 | Custom Field 1 | `transaction.service_custom_field_1` | `service_custom_field1` |
| 19 | Custom Field 2 | `transaction.service_custom_field_2` | `service_custom_field2` |
| 20 | Custom Field 3 | `transaction.service_custom_field_3` | `service_custom_field3` |
| 21 | Custom Field 4 | `transaction.service_custom_field_4` | `service_custom_field4` |

## Export Behavior

### Multiple Products Per Sale
If a sale has multiple products (sell lines), the export creates **one row per product**:

```
Invoice No. | Customer | Product 1 | Qty | Price | Total
INV-001     | John Doe | Product A | 2   | 50    | 150
INV-001     | John Doe | Product B | 1   | 50    | 150
```

Both rows have the same:
- Invoice number
- Customer information
- Order total
- Payment information

This matches the import format, which groups rows by invoice number.

### Empty Sales
If a transaction has no sell lines (shouldn't happen normally), it exports a single row with:
- Transaction information (invoice, customer, totals)
- Empty product fields

## Import Behavior

### Grouping
The import groups rows by a selected column (usually Invoice No.) to create transactions:
- All rows with the same invoice number = 1 transaction
- Each row = 1 product line in that transaction

### Product Matching
Products are matched by:
1. **First priority**: Product SKU (`variation.sub_sku`)
2. **Second priority**: Product name (exact match)

If neither matches, the row is skipped and logged.

### Customer Matching
Customers are matched by:
1. **First priority**: Phone number
2. **Second priority**: Email

If no match, a new customer is created.

### Required Fields
- **Product name OR SKU** (at least one must match existing product)
- **Unit Price** (must be > 0)
- **Business Location** (selected during import)

### Optional Fields
- Quantity (defaults to 1)
- Customer info (defaults to "Walk-in Customer")
- Payment info (defaults to unpaid)
- Tax, discount, description (defaults to empty/0)

## Testing the Round-Trip

To verify export/import compatibility:

1. **Export existing sales**:
   - Go to Sales page
   - Select one or more sales
   - Click "Export (X selected)"
   - Download the Excel file

2. **Verify the format**:
   - Open the Excel file
   - Check that all 21 columns are present
   - Verify data looks correct

3. **Re-import the file**:
   - Go to Import Sales page
   - Upload the exported file
   - Map columns (should auto-match)
   - Select business location
   - Import

4. **Verify the import**:
   - Check Sales page for new sales
   - Compare with original sales
   - Verify all data is preserved

## Current Template

The import template at `public/files/import_sales_template.xlsx` includes:
- Header row with all 21 column names
- Sample data row showing expected format
- Proper date format (YYYY-MM-DD HH:MM:SS)
- Example values for each field

## Files Involved

1. **Export**: `app/Http/Controllers/SellController.php` → `exportForImport()` method
2. **Import**: `app/Http/Controllers/ImportSalesController.php` → `import()` method
3. **Template**: `public/files/import_sales_template.xlsx`
4. **Import Fields**: `ImportSalesController.php` → `__importFields()` method

## Conclusion

✅ **The export and import formats are perfectly aligned.**

You can:
- Export sales from one system and import to another
- Export, modify, and re-import sales
- Use exported files as templates for bulk imports
- Trust that all data will be preserved in the round-trip

The only requirement is that **products must exist** in the target system with matching SKUs or names.
