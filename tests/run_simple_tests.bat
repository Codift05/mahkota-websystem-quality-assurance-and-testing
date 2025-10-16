@echo off
REM Simple Test Runner - No PHPUnit Required

echo ========================================
echo  Mahkota Web System - Simple QA Tests
echo  No PHPUnit or mbstring required!
echo ========================================
echo.

REM Check if PHP is available
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP not found in PATH!
    echo Please make sure Laragon is running.
    echo.
    pause
    exit /b 1
)

echo [INFO] Running simple tests...
echo.

REM Run the simple test runner
php simple_test_runner.php

echo.
pause
