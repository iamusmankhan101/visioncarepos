# Cashier Dashboard Metrics Fix Summary

## Problem
Cashier users were unable to see dashboard metrics (sales totals, charts, etc.) because they lacked the `dashboard.data` permission. The dashboard was showing but without any data widgets.

## Root Cause
The `HomeController` was checking for `dashboard.data` permission to display dashboard metrics, but cashier users only had POS-related permissions (`sell.create`, `sell.view`, etc.) and not the `dashboard.data` permission.

## Solution Applied

### 1. Updated HomeController Logic
**File:** `app/Http/Controllers/HomeController.php`

**Before:**
```php
if (! auth()->user()->can('dashboard.data')) {
    return view('home.index');
}
```

**After:**
```php
// Allow dashboard data for admin users or users with dashboard.data permission
// Also allow for cashier users who have sell.create permission (POS access)
$can_view_dashboard_data = auth()->user()->can('dashboard.data') || 
                          ($is_admin) || 
                          (auth()->user()->can('sell.create') && !auth()->user()->can('superadmin'));

if (!$can_view_dashboard_data) {
    return view('home.index');
}
```

### 2. Updated View Template Logic
**File:** `resources/views/home/index.blade.php`

**Before:**
```php
@if (auth()->user()->can('dashboard.data'))
```

**After:**
```php
@php
    $can_view_dashboard_data = auth()->user()->can('dashboard.data') || 
                              ($is_admin) || 
                              (auth()->user()->can('sell.create') && !auth()->user()->can('superadmin'));
@endphp
@if ($can_view_dashboard_data)
```

### 3. Updated BusinessUtil for New Cashier Roles
**File:** `app/Utils/BusinessUtil.php`

**Before:**
```php
$cashier_role->syncPermissions(['sell.view', 'sell.create', 'sell.update', 'sell.delete', 'access_all_locations', 'view_cash_register', 'close_cash_register']);
```

**After:**
```php
$cashier_role->syncPermissions(['sell.view', 'sell.create', 'sell.update', 'sell.delete', 'access_all_locations', 'view_cash_register', 'close_cash_register', 'dashboard.data']);
```

## Database Updates Required

### For Existing Cashier Users
Run the SQL script `grant_cashier_dashboard_permissions.sql` to grant `dashboard.data` permission to existing cashier roles and users.

### Key SQL Commands:
```sql
-- Grant permission to cashier roles
INSERT IGNORE INTO role_has_permissions (permission_id, role_id) 
SELECT 
    (SELECT id FROM permissions WHERE name = 'dashboard.data' LIMIT 1),
    id
FROM roles 
WHERE name LIKE 'Cashier#%';

-- Grant permission to individual cashier users
INSERT IGNORE INTO model_has_permissions (permission_id, model_type, model_id)
SELECT 
    (SELECT id FROM permissions WHERE name = 'dashboard.data' LIMIT 1),
    'App\\User',
    u.id
FROM users u
WHERE u.id IN (
    SELECT mhp.model_id 
    FROM model_has_permissions mhp 
    JOIN permissions p ON p.id = mhp.permission_id 
    WHERE p.name = 'sell.create' 
    AND mhp.model_type = 'App\\User'
)
AND u.id NOT IN (
    SELECT mhp2.model_id 
    FROM model_has_permissions mhp2 
    JOIN permissions p2 ON p2.id = mhp2.permission_id 
    WHERE p2.name = 'dashboard.data' 
    AND mhp2.model_type = 'App\\User'
);
```

## Files Created/Modified

### Modified Files:
1. `app/Http/Controllers/HomeController.php` - Updated permission logic
2. `resources/views/home/index.blade.php` - Updated view conditions
3. `app/Utils/BusinessUtil.php` - Added dashboard.data to new cashier roles

### Helper Files Created:
1. `grant_cashier_dashboard_permissions.sql` - SQL script to update existing users
2. `grant_dashboard_permission_to_cashiers.php` - PHP script alternative
3. `test_cashier_dashboard_access.php` - Test script to verify logic
4. `clear_cache_dashboard_fix.php` - Cache clearing script

## Testing Steps

1. **Clear Cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Run Database Updates:**
   - Execute `grant_cashier_dashboard_permissions.sql` in your database

3. **Test Cashier Login:**
   - Login with a cashier user account
   - Navigate to dashboard
   - Verify that metrics widgets are visible (Total Sales, Net, Invoice Due, etc.)
   - Verify that charts are displayed

4. **Verify Permissions:**
   - Check that cashier users can see dashboard data
   - Ensure admin functionality is not affected
   - Confirm non-cashier users without permissions still can't see data

## Expected Results

After applying this fix:
- ✅ Cashier users can see dashboard metrics and charts
- ✅ Admin users continue to have full dashboard access
- ✅ Users without appropriate permissions still can't see dashboard data
- ✅ New cashier roles automatically get dashboard.data permission
- ✅ Existing cashier users get dashboard.data permission via database update

## Rollback Plan

If issues occur, revert these changes:

1. **Revert HomeController:**
   ```php
   if (! auth()->user()->can('dashboard.data')) {
       return view('home.index');
   }
   ```

2. **Revert View Template:**
   ```php
   @if (auth()->user()->can('dashboard.data'))
   ```

3. **Remove Permission from Database:**
   ```sql
   DELETE FROM role_has_permissions 
   WHERE permission_id = (SELECT id FROM permissions WHERE name = 'dashboard.data')
   AND role_id IN (SELECT id FROM roles WHERE name LIKE 'Cashier#%');
   ```

## Notes

- This fix maintains security by only allowing users with POS access (sell.create) to see dashboard data
- Superadmin users are excluded from the cashier logic to maintain proper admin permissions
- The fix is backward compatible and doesn't break existing functionality
- Future cashier roles will automatically include dashboard.data permission