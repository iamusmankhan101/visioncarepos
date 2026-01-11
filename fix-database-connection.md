# Database Connection Fix Guide

## Problem
The error `SQLSTATE[HY000] [1524] Plugin 'mysql_native_password' is not loaded` indicates a MySQL authentication plugin issue.

## Quick Fix Steps

### Step 1: Test Current Connection
Run the database test script:
```bash
php test-db-connection.php
```

### Step 2: Update Database Credentials
Update your `.env` file with the correct database information:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vision_care_pos
DB_USERNAME=root
DB_PASSWORD=
```

**Replace these values with your actual database credentials:**
- `DB_DATABASE`: Your actual database name
- `DB_USERNAME`: Your MySQL username (usually 'root' for local development)
- `DB_PASSWORD`: Your MySQL password (leave empty if no password)

### Step 3: Fix MySQL Authentication (if needed)

If you're using MySQL 8.0+, you may need to create a user with the correct authentication plugin:

```sql
-- Connect to MySQL as root
mysql -u root -p

-- Create/update user with mysql_native_password
CREATE USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost';
FLUSH PRIVILEGES;

-- Or if user already exists, alter it:
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';
FLUSH PRIVILEGES;
```

### Step 4: Create Database (if needed)
```sql
CREATE DATABASE IF NOT EXISTS vision_care_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 5: Clear Laravel Cache
After updating the configuration:
```bash
# If you have PHP in PATH:
php artisan config:clear
php artisan cache:clear

# Or manually delete cache files:
# Delete contents of bootstrap/cache/ folder
# Delete contents of storage/framework/cache/ folder
```

## Alternative Solutions

### Option 1: Use Different Database Name
If you have an existing database, update the `.env` file:
```env
DB_DATABASE=your_existing_database_name
```

### Option 2: Use Different MySQL User
Create a new MySQL user specifically for this application:
```sql
CREATE USER 'visioncare'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_password';
GRANT ALL PRIVILEGES ON vision_care_pos.* TO 'visioncare'@'localhost';
FLUSH PRIVILEGES;
```

Then update `.env`:
```env
DB_USERNAME=visioncare
DB_PASSWORD=your_password
```

### Option 3: Use MariaDB Instead
If you're having persistent issues with MySQL 8.0+, consider using MariaDB which has better compatibility with older authentication methods.

## Verification

After making changes:

1. **Test the connection**: Run `php test-db-connection.php`
2. **Check Laravel**: Try accessing your application
3. **Check logs**: Look at `storage/logs/laravel.log` for any remaining errors

## Common Issues

### Issue: "Access denied for user"
- **Solution**: Check username and password in `.env`

### Issue: "Unknown database"
- **Solution**: Create the database or update `DB_DATABASE` in `.env`

### Issue: "Connection refused"
- **Solution**: Make sure MySQL/MariaDB service is running

### Issue: Still getting plugin errors
- **Solution**: Try using `127.0.0.1` instead of `localhost` in `DB_HOST`

## Files Modified
1. `config/database.php` - Updated MySQL options for better compatibility
2. `.env` - Updated database credentials
3. `test-db-connection.php` - Created for testing connection

## Next Steps
Once the database connection is working:
1. The blank receipt issue should be resolved
2. We can then implement the multiple customer printing fix
3. Test the complete functionality