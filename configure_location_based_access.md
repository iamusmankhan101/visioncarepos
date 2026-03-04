# Configure Location-Based Data Access

## 🎯 Problem
After adding new locations, you want to show data only for specific locations instead of showing all data across all locations.

## ✅ Solution
The system already supports location-based filtering through **user permissions**. You need to configure user roles to restrict access to specific locations.

## 📋 Step-by-Step Configuration:

### 1. **Access User Management**
- Go to **Settings** → **User Management** → **Roles**
- Or navigate to `/roles` in your application

### 2. **Edit User Roles**
For each role that should have location restrictions:

1. Click **Edit** on the role (e.g., "Cashier", "Store Manager", etc.)
2. Scroll down to **"Access Locations"** section
3. **Uncheck "All Locations"**
4. **Select only the specific locations** this role should access
5. Click **Save**

### 3. **Assign Users to Roles**
- Go to **User Management** → **Users**
- Edit each user and assign them to the appropriate role
- Users will now only see data from their permitted locations

## 🔧 How It Works:

The system automatically filters all data based on user permissions:

```php
// This code runs automatically for all data queries
$permitted_locations = auth()->user()->permitted_locations();
if ($permitted_locations != 'all') {
    $query->whereIn('transactions.location_id', $permitted_locations);
}
```

## 📊 What Gets Filtered:

When users have location restrictions, they will only see:
- **Sales/Transactions** from their permitted locations
- **Customers** associated with their locations  
- **Products/Inventory** for their locations
- **Reports** filtered to their locations
- **Dashboard data** from their locations only

## 🎛️ Location Dropdown Behavior:

- **Admin users**: See all locations in dropdowns
- **Restricted users**: Only see their permitted locations
- **Single location users**: Location is auto-selected

## 👥 Recommended Role Setup:

### **Admin Role**
- Access: **All Locations**
- Can see and manage all data across all locations

### **Store Manager Role**  
- Access: **Specific Store Location(s)**
- Can manage their assigned store(s) only

### **Cashier Role**
- Access: **Single Location**
- Can only process sales for their specific location

### **Regional Manager Role**
- Access: **Multiple Specific Locations**
- Can oversee several locations in their region

## 🚀 Quick Setup Example:

1. **Create "Store A Manager" role**:
   - Permissions: All sell/purchase permissions
   - Access Locations: ✅ Store A Location only

2. **Create "Store B Cashier" role**:
   - Permissions: Basic sell permissions
   - Access Locations: ✅ Store B Location only

3. **Assign users to these roles**

## ✨ Benefits:

- **Data Security**: Users can't see other locations' data
- **Simplified Interface**: Users only see relevant data
- **Better Performance**: Queries are automatically filtered
- **Compliance**: Meets multi-location business requirements
- **Automatic**: No code changes needed - works immediately

## 🔍 Testing:

1. Create a test user with limited location access
2. Login as that user
3. Check that:
   - Sales list shows only their location's data
   - Reports are filtered to their location
   - Location dropdown shows only permitted locations

## 📝 Notes:

- **Superadmin** always has access to all locations
- Location restrictions apply to **all modules** (sales, purchases, reports, etc.)
- Users can be assigned to **multiple locations** if needed
- Changes take effect immediately after role assignment

This approach gives you complete control over who can see what data, making it perfect for multi-location businesses where each location should operate independently.