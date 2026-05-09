# Optical Prescription Import Fix

## Problem
The optical prescription values (R-Dist-Sph, R-Dist-Cyl, etc.) were showing in the import preview but not being saved to the database after upload.

## Root Cause
The database columns for optical prescription data did not exist in the `contacts` table, and the import function was not reading these columns from the CSV/Excel file.

## Solution Implemented

### 1. Database Migration Created
Created migration file: `database/migrations/2026_05_09_000001_add_optical_prescription_to_contacts_table.php`

This migration adds 10 new columns to the `contacts` table:
- `r_dist_sph` - Right Eye Distance Sphere
- `r_dist_cyl` - Right Eye Distance Cylinder
- `r_dist_axis` - Right Eye Distance Axis
- `r_near_sph` - Right Eye Near Sphere
- `r_near_cyl` - Right Eye Near Cylinder
- `r_near_axis` - Right Eye Near Axis
- `l_dist_sph` - Left Eye Distance Sphere
- `l_dist_cyl` - Left Eye Distance Cylinder
- `l_dist_axis` - Left Eye Distance Axis
- `l_near_sph` - Left Eye Near Sphere

### 2. Import Function Updated
Updated `app/Http/Controllers/ContactController.php` - `postImportContacts()` method to read and save the optical prescription columns from the import file (columns 27-36).

### 3. Template Files Updated
Updated both import template files:
- `public/files/import_contacts_csv_template.csv`
- `public/files/import_contacts_csv_template.xls`

Both files now include the optical prescription columns (without the custom field columns that were removed).

## Required Action

**You must run the database migration to add the new columns:**

```bash
php artisan migrate
```

This will add the 10 optical prescription columns to your `contacts` table.

## Testing

After running the migration:

1. Download the updated template from the import page
2. Fill in contact data including optical prescription values
3. Upload the file
4. Verify the preview shows all columns including optical prescription data
5. Confirm the import
6. Check the contact record to ensure optical prescription values are saved

## Column Mapping

The import file columns are mapped as follows:

| Column # | Field Name | Database Column |
|----------|------------|-----------------|
| 1-26 | Standard contact fields | (existing) |
| 27 | R-Dist-Sph | r_dist_sph |
| 28 | R-Dist-Cyl | r_dist_cyl |
| 29 | R-Dist-Axis | r_dist_axis |
| 30 | R-Near-Sph | r_near_sph |
| 31 | R-Near-Cyl | r_near_cyl |
| 32 | R-Near-Axis | r_near_axis |
| 33 | L-Dist-Sph | l_dist_sph |
| 34 | L-Dist-Cyl | l_dist_cyl |
| 35 | L-Dist-Axis | l_dist_axis |
| 36 | L-Near-Sph | l_near_sph |

## Notes

- All optical prescription fields are nullable (optional)
- The Contact model uses `$guarded = ['id']`, so all new columns are automatically fillable
- The preview template dynamically displays all columns, so no changes were needed there
