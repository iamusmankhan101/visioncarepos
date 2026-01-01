@echo off
echo === HOSTINGER SSH DEPLOYMENT ===
echo.

REM Configuration - Update these with your actual details
set HOSTINGER_HOST=your-domain.com
set HOSTINGER_USER=your-username
set HOSTINGER_PATH=/domains/your-domain.com/public_html

echo WARNING: Please update the configuration in this script first!
echo.
echo Edit this file and replace:
echo   HOSTINGER_HOST with your actual domain
echo   HOSTINGER_USER with your SSH username  
echo   HOSTINGER_PATH with your actual web root path
echo.

set /p confirm="Have you updated the configuration? (y/n): "
if /i not "%confirm%"=="y" (
    echo Please update the configuration first, then run the script again.
    pause
    exit /b 1
)

echo.
echo Pushing changes to GitHub...
git push origin main

echo.
echo Deploying to Hostinger via SSH...
echo Connecting to %HOSTINGER_HOST%...

REM Create SSH command
ssh %HOSTINGER_USER%@%HOSTINGER_HOST% "cd %HOSTINGER_PATH% && git pull origin main && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear && chmod -R 755 storage bootstrap/cache"

if %errorlevel% equ 0 (
    echo.
    echo ✅ DEPLOYMENT SUCCESSFUL!
    echo.
    echo Your application has been updated at: https://%HOSTINGER_HOST%
    echo.
    echo What was deployed:
    echo   • Related customer functionality fixes
    echo   • Form validation improvements  
    echo   • Modal handling enhancements
    echo   • AJAX form submission fixes
    echo   • White screen issue resolution
) else (
    echo.
    echo ❌ DEPLOYMENT FAILED!
    echo Please check the error messages above and try again.
)

echo.
pause