# Quick Start Guide - Testing

Panduan cepat untuk mulai testing dalam 5 menit.

## ⚡ Quick Setup (5 Menit)

### Step 1: Install Dependencies (2 menit)

```bash
cd tests
composer install
```

### Step 2: Konfigurasi Database (1 menit)

Edit `phpunit.xml` jika perlu (default sudah OK):

```xml
<env name="DB_HOST" value="localhost"/>
<env name="DB_USER" value="root"/>
<env name="DB_PASS" value=""/>
<env name="DB_NAME" value="mahkota_test"/>
```

### Step 3: Run Tests (2 menit)

**Windows**:
```cmd
run_tests.bat
```

**Linux/Mac**:
```bash
chmod +x run_tests.sh
./run_tests.sh
```

**Manual**:
```bash
vendor/bin/phpunit --testdox
```

## 📊 Expected Output

```
PHPUnit 9.5.x by Sebastian Bergmann and contributors.

Artikel Create Test
 ✔ Create artikel success
 ✔ Create artikel without judul
 ✔ Create artikel without kategori
 ...

Time: 00:05.123, Memory: 10.00 MB

OK (72 tests, 150 assertions)
```

## 🎯 Common Commands

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/api/ArtikelCreateTest.php

# Run specific test method
vendor/bin/phpunit --filter testCreateArtikelSuccess

# Run with coverage
vendor/bin/phpunit --coverage-html coverage

# Run only API tests
vendor/bin/phpunit --testsuite "API Tests"

# Run only Integration tests
vendor/bin/phpunit --testsuite "Integration Tests"
```

## 🐛 Troubleshooting

### Error: "vendor/bin/phpunit not found"
**Fix**: Run `composer install`

### Error: "Database connection failed"
**Fix**: 
1. Start MySQL: `net start MySQL` (Windows) or `sudo service mysql start` (Linux)
2. Check credentials in `phpunit.xml`

### Error: "Class not found"
**Fix**: Run `composer dump-autoload`

## 📁 File Structure

```
tests/
├── api/              # Unit tests (36 tests)
├── integration/      # Integration tests (8 tests)
├── helpers/          # Helper classes
├── phpunit.xml       # Configuration
└── README.md         # Full documentation
```

## ✅ What's Tested?

- ✅ **Artikel CRUD** (36 tests)
- ✅ **Galeri CRUD** (13 tests)
- ✅ **Program CRUD** (15 tests)
- ✅ **Integration** (8 tests)
- ✅ **Total: 72 test cases**

## 🚀 Next Steps

1. ✅ Setup selesai? Baca [README.md](README.md) untuk detail lengkap
2. 📖 Pelajari [TESTING_GUIDE.md](TESTING_GUIDE.md) untuk best practices
3. 🔧 Customize tests sesuai kebutuhan project

## 💡 Tips

- Run tests sebelum commit code
- Check coverage report: `vendor/bin/phpunit --coverage-html coverage`
- Add new tests untuk fitur baru
- Keep tests fast dan isolated

---

**Happy Testing! 🎉**
