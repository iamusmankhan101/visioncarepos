@echo off
echo ========================================
echo Deploying to Hostinger via Git
echo ========================================
echo.

REM Connect to Hostinger and pull latest changes
ssh u275675839@156.67.218.107 "cd domains/digitrot.com/public_html/pos && git pull origin main && php artisan cache:clear && php artisan config:clear && php artisan view:clear"

echo.
echo ========================================
echo Deployment Complete!
echo ========================================
echo Visit: https://pos.digitrot.com
echo.
pause
