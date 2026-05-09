# Optical Prescription Import Fix

## Problem
The optical prescription values (R-Dist-Sph, R-Dist-Cyl, etc.) were showing in the import preview but not being saved to the database after upload. Additionally, the values were appearing in the wrong fields (address fields).

## Root Cause
1. The database columns for optical prescription data did not exist in the `contacts` table
2. The import function was not reading these columns from the CSV/Excel file
3. The CSV template had an incorrect column structure that didn't match what the import code expected

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
Updated `app/Http/Controllers/ContactController.php` - `postImportContacts()` method to read and save the optical prescription columns from the import file (columns 28-37).

### 3. Template Files Fixed
Updated both import template files with the CORRECT column structure:
- `public/files/import_contacts_csv_template.csv`
- `public/files/import_contacts_csv_template.xls`

The templates now have 37 columns in the correct order matching what the import code expects.

### 4. Import Instructions Updated
Updated `resources/views/contact/import.blade.php` to show instructions for columns 28-37 (optical prescription fields).

## Required Action

**You must run the database migration to add the new columns:**

```bash
composer install  # If vendor folder is missing
php artisan migrate
```

This will add the 10 optical prescription columns to your `contacts` table.

## Correct Template Structure

The import template now has the following column order (37 columns total):

| Column # | Field Name | Required |
|----------|------------|----------|
| 1 | CONTACT TYPE | Required |
| 2 | PREFIX | Optional |
| 3 | FIRST NAME | Required |
| 4 | MIDDLE NAME | Optional |
| 5 | LAST NAME | Optional |
| 6 | BUSINESS NAME | Required if Supplier |
| 7 | CONTACT ID | Optional |
| 8 | TAX NUMBER | Optional |
| 9 | OPENING BALANCE | Optional |
| 10 | PAY TERM | Required if Supplier |
| 11 | PAY TERM PERIOD | Required if Supplier |
| 12 | CREDIT LIMIT | Optional |
| 13 | EMAIL | Optional |
| 14 | MOBILE | Required |
| 15 | ALT. CONTACT NO. | Optional |
| 16 | LANDLINE | Optional |
| 17 | CITY | Optional |
| 18 | STATE | Optional |
| 19 | COUNTRY | Optional |
| 20 | ADDRESS LINE 1 | Optional |
| 21 | ADDRESS LINE 2 | Optional |
| 22 | ZIP CODE | Optional |
| 23 | DOB | Optional |
| 24 | CUSTOM FIELD 1 | Optional |
| 25 | CUSTOM FIELD 2 | Optional |
| 26 | CUSTOM FIELD 3 | Optional |
| 27 | CUSTOM FIELD 4 | Optional |
| 28 | R-Dist-Sph | Optional |
| 29 | R-Dist-Cyl | Optional |
| 30 | R-Dist-Axis | Optional |
| 31 | R-Near-Sph | Optional |
| 32 | R-Near-Cyl | Optional |
| 33 | R-Near-Axis | Optional |
| 34 | L-Dist-Sph | Optional |
| 35 | L-Dist-Cyl | Optional |
| 36 | L-Dist-Axis | Optional |
| 37 | L-Near-Sph | Optional |

## Testing

After running the migration:

1. Download the updated template from the import page
2. Fill in contact data including optical prescription values
3. Upload the file
4. Verify the preview shows all columns including optical prescription data
5. Confirm the import
6. Check the contact record to ensure optical prescription values are saved in the correct fields

## Notes

- All optical prescription fields are nullable (optional)
- The Contact model uses `$guarded = ['id']`, so all new columns are automatically fillable
- The preview template dynamically displays all columns, so no changes were needed there
- The template structure now matches exactly what the import code expects
