# Primary/Secondary Labels Fix

## Issue Fixed
Multiple customers were showing "Primary" labels when only the main customer (lowest ID) should show "Primary" and others should show "Secondary".

## Updated Implementation

### 1. **POS Customer Dropdown** (`public/js/pos.js`)
✅ **Updated Logic**
```javascript
if (data.has_related_customers && data.has_related_customers > 0) {
    if (data.id == data.phone_group_primary_id) {
        // Primary customer (lowest ID)
        template += ' <span class="label label-primary">Primary</span>';
    } else {
        // Secondary customer (not the lowest ID)
        template += ' <span class="label label-success">Secondary</span>';
    }
}
```

### 2. **POS Related Customers Modal** (`public/js/pos.js`)
✅ **Updated Logic**
```javascript
var isPrimaryBadge = customer.is_primary ? 
    '<span class="label label-success">Primary</span>' : 
    '<span class="label label-warning">Secondary</span>';
```

### 3. **Contact Edit Page** (`resources/views/contact/edit.blade.php`)
✅ **Updated Logic**
```php
@if(!empty($related['is_primary']))
    <span class="label label-success">Primary</span>
@else
    <span class="label label-warning">Secondary</span>
@endif
```

## Label Color Scheme

### **Primary Customer (Lowest ID)**
- 🔵 **Blue "Primary"** in POS dropdown
- 🟢 **Green "Primary"** in POS modal and edit page

### **Secondary Customers (Higher IDs)**
- 🟢 **Green "Secondary"** in POS dropdown  
- 🟡 **Orange "Secondary"** in POS modal and edit page

### **Other Labels**
- 🔵 **Blue "Currently Selected"** in POS modal (for selected customer)
- 🔵 **Blue "Family"** in edit page (relationship type)

## Expected Behavior

### **Example: 3 Customers with Same Phone**
- Customer A (ID: 100) → **Primary** (lowest ID)
- Customer B (ID: 105) → **Secondary** 
- Customer C (ID: 110) → **Secondary**

### **POS Dropdown Display**
```
usman khan (CO0057) [Primary]
Mobile: 03058562523

usman (CO0058) [Secondary] 
Mobile: 03058562523

raza (CO0059) [Secondary]
Mobile: 03058562523
```

### **POS Modal Display**
- Customer A: "Primary" (green) + "Currently Selected" (blue) if selected
- Customer B: "Secondary" (orange)
- Customer C: "Secondary" (orange)

### **Contact Edit Page**
- Current contact title shows "Primary" if it's the primary customer
- Related customers show "Primary" or "Secondary" based on their ID

## Benefits

✅ **Clear Hierarchy**: Only one customer shows as "Primary"
✅ **Visual Distinction**: Different colors for primary vs secondary
✅ **Consistent Logic**: Same rules across all interfaces
✅ **User Friendly**: Easy to identify the main customer account

The labeling system now correctly identifies and displays only the main customer as "Primary" while all related customers show as "Secondary"!