@echo off
echo ========================================
echo 🚀 ABSENSI QRCODE - LOCAL SETUP
echo ========================================

echo 1. Stopping any running server...
taskkill /F /IM php.exe 2>nul

echo 2. Creating .env file...
if not exist .env (
    copy .env.example .env
    echo ✅ .env created
) else (
    echo ⚠️  .env already exists
)

echo 3. Generating application key...
php artisan key:generate

echo 4. Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo 5. Creating storage link...
php artisan storage:link

echo 6. Starting development server...
echo.
echo 🌐 OPEN THIS URL IN BROWSER:
echo http://localhost:8000
echo.
echo 👤 DEFAULT LOGIN:
echo Admin: admin / admin123
echo Karyawan: budi / karyawan123
echo.
php artisan serve --host=127.0.0.1 --port=8000
pause
