@echo off
REM Batch script untuk menjalankan PHPUnit tests di Windows

echo ========================================
echo  Mahkota Web System - QA Testing
echo ========================================
echo.

REM Check if vendor directory exists
if not exist "vendor\" (
    echo [ERROR] Vendor directory not found!
    echo Please run: composer install
    echo.
    pause
    exit /b 1
)

REM Check if PHPUnit is installed
if not exist "vendor\bin\phpunit" (
    echo [ERROR] PHPUnit not found!
    echo Please run: composer install
    echo.
    pause
    exit /b 1
)

echo [INFO] Running PHPUnit tests...
echo.

REM Run PHPUnit with testdox format
vendor\bin\phpunit --testdox --colors=always

echo.
echo ========================================
echo  Test Execution Completed
echo ========================================
echo.

pause
