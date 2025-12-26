@echo off
echo ========================================
echo Vision Care POS - Heroku Deployment
echo ========================================
echo.

echo Step 1: Adding Heroku remote...
heroku git:remote -a visioncarepos
echo.

echo Step 2: Setting buildpack...
heroku buildpacks:set heroku/php
echo.

echo Step 3: Adding PostgreSQL database...
heroku addons:create heroku-postgresql:mini
echo.

echo Step 4: Setting environment variables...
heroku config:set APP_KEY=base64:G0uwjZVbFp3dq0Syr44hofsIAZ3Fi/kC/JC4NvlNHs8=
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
heroku config:set APP_URL=https://visioncarepos-960a68b74f80.herokuapp.com
heroku config:set DB_CONNECTION=pgsql
echo.

echo Step 5: Pushing code to Heroku...
git push heroku main
echo.

echo Step 6: Running migrations...
heroku run php artisan migrate --force
heroku run php artisan db:seed --force
echo.

echo Step 7: Clearing cache...
heroku run php artisan config:cache
heroku run php artisan route:cache
echo.

echo ========================================
echo Deployment Complete!
echo Your app should be live at:
echo https://visioncarepos-960a68b74f80.herokuapp.com
echo ========================================
pause
