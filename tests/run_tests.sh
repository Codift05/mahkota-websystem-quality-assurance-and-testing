#!/bin/bash
# Shell script untuk menjalankan PHPUnit tests di Linux/Mac

echo "========================================"
echo " Mahkota Web System - QA Testing"
echo "========================================"
echo ""

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "[ERROR] Vendor directory not found!"
    echo "Please run: composer install"
    echo ""
    exit 1
fi

# Check if PHPUnit is installed
if [ ! -f "vendor/bin/phpunit" ]; then
    echo "[ERROR] PHPUnit not found!"
    echo "Please run: composer install"
    echo ""
    exit 1
fi

echo "[INFO] Running PHPUnit tests..."
echo ""

# Run PHPUnit with testdox format
./vendor/bin/phpunit --testdox --colors=always

echo ""
echo "========================================"
echo " Test Execution Completed"
echo "========================================"
echo ""
