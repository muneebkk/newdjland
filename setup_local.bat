@echo off
echo DJ Land Local Setup Script
echo =========================

echo.
echo 1. Creating config.php from sample...
if not exist "config.php" (
    copy "config.php.sample" "config.php"
    echo ✓ config.php created
) else (
    echo ✓ config.php already exists
)

echo.
echo 2. Creating Laravel .env file...
if not exist "app\api2\.env" (
    echo APP_ENV=local > app\api2\.env
    echo APP_DEBUG=true >> app\api2\.env
    echo APP_KEY=base64:your-random-key-here >> app\api2\.env
    echo APP_URL=http://localhost >> app\api2\.env
    echo. >> app\api2\.env
    echo DB_CONNECTION=mysql >> app\api2\.env
    echo DB_HOST=127.0.0.1 >> app\api2\.env
    echo DB_PORT=3306 >> app\api2\.env
    echo DB_DATABASE=djland >> app\api2\.env
    echo DB_USERNAME=root >> app\api2\.env
    echo DB_PASSWORD= >> app\api2\.env
    echo. >> app\api2\.env
    echo CACHE_DRIVER=file >> app\api2\.env
    echo SESSION_DRIVER=file >> app\api2\.env
    echo QUEUE_DRIVER=sync >> app\api2\.env
    echo ✓ Laravel .env file created
) else (
    echo ✓ Laravel .env file already exists
)

echo.
echo 3. Installing Composer dependencies...
cd app\api2
if exist "composer.phar" (
    php composer.phar install
) else (
    composer install
)
cd ..\..

echo.
echo Setup complete!
echo.
echo Next steps:
echo 1. Make sure XAMPP/WAMP is running (Apache + MySQL)
echo 2. Create a database called 'djland' in phpMyAdmin
echo 3. Import the database structure by visiting: http://localhost/your-project/setup/setup_database.php
echo 4. Edit config.php with your database credentials
echo 5. Generate Laravel app key: cd app/api2 && php artisan key:generate
echo 6. Access the application at: http://localhost/your-project/
echo.
echo Default admin login:
echo Username: admin
echo Password: pass
echo.
pause 