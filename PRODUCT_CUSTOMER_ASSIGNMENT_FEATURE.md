# Product-Customer Assignment Feature

## Overview
This feature allows you to assign specific products to specific customers when making a purchase with multiple customers in the POS screen. Each customer's invoice will only show the products assigned to them.

## How It Works

### 1. POS Screen
When you add products to a POS transaction with multiple customers:
- A new "Customer" column appears in the product table
- Each product row has a dropdown to select which customer the product is for
- The dropdown is automatically populated with all selected customers

### 2. Customer Selection
- Select the primary customer as usual
- Add products to the cart
- For each product, select which customer it's for from the dropdown
- If only one customer is selected, the product is automatically assigned to that customer

### 3. Invoice Generation
- When generating invoices, products are filtered by customer
- Each customer's invoice only shows their assigned products
- The receipt shows product assignments clearly

## Database Changes

### Migration
A new migration adds the `assigned_customer_id` column to the `transaction_sell_lines` table:

```sql
ALTER TABLE transaction_sell_lines 
ADD COLUMN assigned_customer_id INT UNSIGNED NULL AFTER res_service_staff_id,
ADD FOREIGN KEY (assigned_customer_id) REFERENCES contacts(id) ON DELETE SET NULL,
ADD INDEX (assigned_customer_id);
```

### Running the Migration
```bash
php artisan migrate
```

## Files Modified

### 1. Frontend Files
- `resources/views/sale_pos/partials/pos_form.blade.php` - Added Customer column to product table
- `resources/views/sale_pos/product_row.blade.php` - Added customer dropdown to each product row
- `resources/views/sale_pos/create.blade.php` - Included the new JavaScript module
- `public/js/pos_product_customer_assignment.js` - New JavaScript module for handling assignments

### 2. Backend Files
- `app/Utils/TransactionUtil.php` - Updated to save and retrieve assigned_customer_id
- `database/migrations/2026_03_04_000001_add_assigned_customer_to_transaction_sell_lines.php` - Migration file

## JavaScript API

### Global Functions

#### `window.addCustomerToSelection(customerId, customerName)`
Adds a customer to the selection list
```javascript
window.addCustomerToSelection(123, 'John Doe');
```

#### `window.removeCustomerFromSelection(customerId)`
Removes a customer from the selection
```javascript
window.removeCustomerFromSelection(123);
```

#### `window.clearCustomerSelections()`
Clears all customer selections
```javascript
window.clearCustomerSelections();
```

#### `window.getProductCustomerAssignments()`
Returns an array of product-customer assignments
```javascript
var assignments = window.getProductCustomerAssignments();
// Returns: [{product_id: 1, variation_id: 2, customer_id: 123, quantity: 2, row_index: 0}, ...]
```

#### `window.validateProductCustomerAssignments()`
Validates that all products have customer assignments (when multiple customers are selected)
```javascript
if (window.validateProductCustomerAssignments()) {
    // All products are assigned
} else {
    // Some products are not assigned
}
```

## Usage Example

### Scenario: Two customers buying different products

1. **Select Primary Customer**
   - Select "John Doe" as the primary customer

2. **Add Products**
   - Add "Eyeglasses Frame A" to cart
   - Add "Contact Lenses B" to cart

3. **Assign Products**
   - For "Eyeglasses Frame A", select "John Doe" from the customer dropdown
   - For "Contact Lenses B", select "Jane Smith" from the customer dropdown

4. **Complete Sale**
   - Process payment as usual
   - Generate invoices

5. **Result**
   - John Doe's invoice shows only "Eyeglasses Frame A"
   - Jane Smith's invoice shows only "Contact Lenses B"

## Validation

### Automatic Validation
- When multiple customers are selected, the system validates that all products have customer assignments before submission
- If any product is unassigned, a warning message is displayed
- The form submission is prevented until all products are assigned

### Manual Validation
You can manually validate assignments using:
```javascript
if (!window.validateProductCustomerAssignments()) {
    alert('Please assign all products to customers');
}
```

## Receipt/Invoice Display

### Primary Customer Receipt
- Shows products assigned to the primary customer
- Displays other customers' names for reference
- Each additional customer's section shows their assigned products

### Additional Customer Receipts
- Each additional customer gets their own receipt
- Only shows products assigned to them
- Includes their prescription details (if applicable)

## Troubleshooting

### Products not showing customer dropdown
- Ensure the JavaScript file is loaded: `public/js/pos_product_customer_assignment.js`
- Check browser console for errors
- Verify the migration has been run

### Customer dropdown is empty
- Ensure a customer is selected in the main customer field
- Check that `window.posSelectedCustomers` array is populated
- Open browser console and type: `console.log(window.posSelectedCustomers)`

### Assignments not saving
- Check that the `assigned_customer_id` column exists in `transaction_sell_lines` table
- Verify the migration was run successfully
- Check server logs for any errors

## Future Enhancements

Potential improvements for this feature:
1. Bulk assign products to a customer
2. Copy assignments from previous transactions
3. Visual indicators showing which products are assigned to which customers
4. Quick assign buttons for common scenarios
5. Product assignment templates

## Support

For issues or questions about this feature:
1. Check the browser console for JavaScript errors
2. Check server logs for backend errors
3. Verify database migration was successful
4. Review this documentation for proper usage

## Version History

- **v1.0.0** (2026-03-04) - Initial release
  - Added customer column to POS product table
  - Implemented product-customer assignment dropdowns
  - Created JavaScript module for handling assignments
  - Added database migration for assigned_customer_id
  - Updated receipt generation to filter by customer
