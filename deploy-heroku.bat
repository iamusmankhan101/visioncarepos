@echo off
echo ========================================
echo Vision Care POS - Heroku Deployment
echo ========================================
echo.

echo Step 1: Adding Heroku remote...
heroku git:remote -a visioncarepos-969e6857489f
echo.

echo Step 2: Setting buildpack...
heroku buildpacks:set heroku/php -a visioncarepos-969e6857489f
echo.

echo Step 3: Adding PostgreSQL database...
heroku addons:create heroku-postgresql:mini -a visioncarepos-969e6857489f
echo.

echo Step 4: Setting environment variables...
heroku config:set APP_KEY=base64:9L70ec7VBxWRbKlGSzUtHdGI5bs8oEsi7oF3g8gv+WU= -a visioncarepos-969e6857489f
heroku config:set APP_ENV=production -a visioncarepos-969e6857489f
heroku config:set APP_DEBUG=false -a visioncarepos-969e6857489f
heroku config:set APP_URL=https://visioncarepos-969e6857489f.herokuapp.com -a visioncarepos-969e6857489f
heroku config:set DB_CONNECTION=pgsql -a visioncarepos-969e6857489f
heroku config:set LOG_CHANNEL=errorlog -a visioncarepos-969e6857489f
echo.

echo Step 5: Pushing code to Heroku...
git push heroku main
echo.

echo Step 6: Running migrations...
heroku run php artisan migrate --force -a visioncarepos-969e6857489f
echo.

echo Step 7: Clearing cache...
heroku run php artisan config:cache -a visioncarepos-969e6857489f
heroku run php artisan route:cache -a visioncarepos-969e6857489f
echo.

echo ========================================
echo Deployment Complete!
echo Your app should be live at:
echo https://visioncarepos-969e6857489f.herokuapp.com
echo ========================================
pause
