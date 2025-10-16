@echo off
REM Script untuk download PHPUnit PHAR secara manual

echo ========================================
echo  Download PHPUnit Manual
echo ========================================
echo.

echo [INFO] Downloading PHPUnit 9.5 PHAR...
echo.

REM Download using PowerShell
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://phar.phpunit.de/phpunit-9.5.phar' -OutFile 'phpunit.phar'}"

if exist phpunit.phar (
    echo.
    echo [SUCCESS] PHPUnit downloaded successfully!
    echo.
    echo [INFO] Testing PHPUnit...
    php phpunit.phar --version
    echo.
    echo ========================================
    echo  Installation Complete
    echo ========================================
    echo.
    echo You can now run tests with:
    echo   php phpunit.phar --testdox
    echo.
) else (
    echo.
    echo [ERROR] Download failed!
    echo Please download manually from: https://phar.phpunit.de/phpunit-9.5.phar
    echo Save as: phpunit.phar in this folder
    echo.
)

pause
