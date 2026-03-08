# Prescription Source Feature - Implementation Summary

## Overview
Added a prescription source selection feature to the customer add/edit forms. This allows users to indicate whether a customer's prescription was provided by Vision Care or from an external source.

## Feature Details

### User Interface
- **Location**: Near the lens prescription section in customer forms
- **Type**: Radio button group (mutually exclusive options)
- **Options**:
  1. "Prescription by Vision Care" (value: `vision_care`)
  2. "Prescription not by Vision Care" (value: `not_vision_care`)

### Visual Design
- Icon indicators for each option
- Color-coded labels (blue for Vision Care, gray for external)
- Positioned above the prescription table for easy access

## Implementation

### Files Modified

#### 1. View Files
- **resources/views/contact/edit.blade.php**
  - Added prescription source radio buttons in main form (line ~246)
  - Added prescription source radio buttons in inline "Add Related Customer" form (line ~424)
  - Updated JavaScript to include `related_prescription_source` in AJAX submission

- **resources/views/contact/create.blade.php**
  - Added prescription source radio buttons in customer creation form (line ~460)

#### 2. Controller
- **app/Http/Controllers/ContactController.php**
  - **store() method**: Added handling to save `prescription_source` to `shipping_custom_field_details`
  - **update() method**: Added handling to save `prescription_source` to `shipping_custom_field_details`
  - **storeRelatedCustomer() method**: Added handling for `related_prescription_source`

### Data Storage
- **Field Name**: `prescription_source`
- **Storage Location**: `shipping_custom_field_details` JSON column in `contacts` table
- **Values**: 
  - `vision_care` - Prescription by Vision Care
  - `not_vision_care` - Prescription not by Vision Care

## Usage

### Adding a New Customer
1. Navigate to Contacts > Add Customer
2. Expand "More Info" section
3. Scroll to "Lens Prescription" section
4. Select the appropriate prescription source option
5. Fill in prescription details
6. Save the customer

### Editing an Existing Customer
1. Open customer edit form
2. Expand "More Info" section
3. Scroll to "Lens Prescription" section
4. The previously selected option will be pre-selected (if any)
5. Change selection if needed
6. Save changes

### Adding Related Customers
1. In customer edit form, click "Add Another Related Customer"
2. Fill in customer details
3. In the "Lens Prescription" section, select prescription source
4. Fill in prescription details
5. Click "Save Related Customer"

## Technical Notes

### Database Schema
No database migration required. The feature uses the existing `shipping_custom_field_details` JSON column which is already present in the `contacts` table.

### Backward Compatibility
- Existing customers without a prescription source value will show no selection
- The field is optional - users can leave it unselected
- No data migration needed

### Access in Code
To retrieve the prescription source for a contact:

```php
$contact = Contact::find($id);
$prescription_source = $contact->shipping_custom_field_details['prescription_source'] ?? null;

// Check if prescription is by Vision Care
if ($prescription_source === 'vision_care') {
    // Handle Vision Care prescription
}
```

### Display in Blade Templates
```blade
@if(!empty($contact->shipping_custom_field_details['prescription_source']))
    @if($contact->shipping_custom_field_details['prescription_source'] == 'vision_care')
        <span class="label label-primary">Prescription by Vision Care</span>
    @else
        <span class="label label-default">Prescription not by Vision Care</span>
    @endif
@endif
```

## Testing Checklist

- [x] Add new customer with prescription source selected
- [x] Edit existing customer and change prescription source
- [x] Add related customer with prescription source
- [x] Verify data is saved correctly in database
- [x] Verify previously selected value is displayed when editing
- [x] Test with no selection (optional field)

## Future Enhancements

Potential improvements for future versions:
1. Display prescription source on invoices/receipts
2. Add prescription source filter in customer list
3. Generate reports based on prescription source
4. Add prescription source to customer export
5. Show prescription source in customer details view

## Related Features

This feature complements:
- Lens prescription fields (custom_field1-10)
- Related customer management
- Customer grouping by phone number
- Product-customer assignment in POS

## Support

For questions or issues related to this feature, refer to:
- Customer management documentation
- Contact form customization guide
- Custom fields documentation
