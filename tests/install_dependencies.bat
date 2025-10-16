@echo off
REM Script untuk install dependencies dengan disable TLS check (untuk development)

echo ========================================
echo  Installing Composer Dependencies
echo ========================================
echo.

echo [INFO] Installing PHPUnit and dependencies...
echo [WARNING] Using --no-secure-http for local development only
echo.

set COMPOSER_DISABLE_NETWORK=0
C:\laragon\bin\composer\composer.bat config -g -- disable-tls true
C:\laragon\bin\composer\composer.bat install --no-secure-http

echo.
echo ========================================
echo  Installation Complete
echo ========================================
echo.

pause
