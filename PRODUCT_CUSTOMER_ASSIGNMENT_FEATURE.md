# Product-Customer Assignment Feature

## Overview
This feature allows you to assign specific products to specific customers when making a purchase with multiple customers in the POS screen. Each customer's invoice will only show the products assigned to them.

**NEW:** The system now automatically includes related customers (family members) who share the same phone number, making it easy to assign products to different family members in a single transaction.

## How It Works

### 1. POS Screen
When you add products to a POS transaction:
- Select a customer from the main customer dropdown
- **The system automatically fetches and includes all related customers (family members)**
- A new "Customer" column appears in the product table
- Each product row has a dropdown showing the main customer and all related customers
- The dropdown is automatically populated with all family members

### 2. Related Customers (Family Members)
- **Automatic Detection:** When you select a customer, the system automatically finds all related customers who share the same phone number
- **Visual Indicators:** 
  - Primary customer is marked with "(Primary)" badge
  - Prescription information is shown for each customer (if available)
- **Smart Assignment:** Products are auto-assigned to the primary/current customer by default

### 3. Customer Selection
- Select the primary customer as usual
- **Related customers are automatically loaded** - no manual action needed
- Add products to the cart
- For each product, select which customer (or family member) it's for from the dropdown
- If only one customer exists, the product is automatically assigned to that customer

### 4. Invoice Generation
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

#### `window.refreshRelatedCustomers()`
Refreshes the related customers list for the current selection
```javascript
window.refreshRelatedCustomers();
```

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

### Scenario: Family purchase with related customers

1. **Select Primary Customer**
   - Select "John Doe" as the primary customer
   - **System automatically finds related customers:**
     - John Doe (Primary) - R: -1.50/-0.75/180
     - Jane Doe - R: -2.00/-1.00/90
     - Jimmy Doe - R: -0.50/-0.25/180

2. **Add Products**
   - Add "Eyeglasses Frame A" to cart
   - Add "Contact Lenses B" to cart
   - Add "Reading Glasses C" to cart

3. **Assign Products to Family Members**
   - For "Eyeglasses Frame A", select "John Doe (Primary)" from the customer dropdown
   - For "Contact Lenses B", select "Jane Doe" from the customer dropdown
   - For "Reading Glasses C", select "Jimmy Doe" from the customer dropdown

4. **Complete Sale**
   - Process payment as usual
   - Generate invoices

5. **Result**
   - John Doe's invoice shows only "Eyeglasses Frame A"
   - Jane Doe's invoice shows only "Contact Lenses B"
   - Jimmy Doe's invoice shows only "Reading Glasses C"
   - All family members' prescriptions are included in their respective invoices

## Related Customers (Family Members)

### How Related Customers Work
- **Phone-Based Grouping:** Customers are automatically grouped by their phone number
- **Automatic Detection:** When you select a customer, all related customers with the same phone number are automatically loaded
- **Visual Identification:**
  - Primary customer is marked with "(Primary)" badge
  - Current selected customer is highlighted
  - Prescription information is displayed for each customer

### Benefits
1. **Family Purchases:** Easily handle purchases for entire families in one transaction
2. **Quick Assignment:** All family members appear in the dropdown automatically
3. **Accurate Records:** Each family member gets their own invoice with their products
4. **Prescription Tracking:** Prescription information is shown for easy identification

### Example
If you select "John Doe" who has phone number "555-1234", the system will automatically find:
- John Doe (Primary) - R: -1.50/-0.75/180 | L: -1.50/-0.75/180
- Jane Doe - R: -2.00/-1.00/90 | L: -2.00/-1.00/90
- Jimmy Doe - R: -0.50/-0.25/180 | L: -0.50/-0.25/180

All three will appear in the customer dropdown for product assignment.

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
- Verify the related customers API is working: Check Network tab for `/contacts/{id}/related-customers` request

### Related customers not loading
- Check browser console for AJAX errors
- Verify the route exists: `/contacts/{id}/related-customers`
- Check that customers have the same phone number
- Ensure customers have `contact_status = 'active'`
- Check server logs for any errors

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
