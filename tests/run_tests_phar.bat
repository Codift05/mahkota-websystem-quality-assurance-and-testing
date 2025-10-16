@echo off
REM Batch script untuk menjalankan PHPUnit tests menggunakan PHAR

echo ========================================
echo  Mahkota Web System - QA Testing
echo  Using PHPUnit PHAR
echo ========================================
echo.

REM Check if phpunit.phar exists
if not exist "phpunit.phar" (
    echo [ERROR] phpunit.phar not found!
    echo Please run: .\download_phpunit.bat
    echo.
    pause
    exit /b 1
)

echo [INFO] Running PHPUnit tests with PHAR...
echo.

REM Run PHPUnit with testdox format
php phpunit.phar --testdox --colors=always

echo.
echo ========================================
echo  Test Execution Completed
echo ========================================
echo.

pause
