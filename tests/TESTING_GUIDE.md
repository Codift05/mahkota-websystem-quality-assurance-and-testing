# Panduan Testing untuk QA Engineer

Panduan lengkap untuk QA Engineer dalam melakukan testing pada Mahkota Web System.

## 🎯 Tujuan Testing

1. **Memastikan Fungsionalitas**: Semua fitur CRUD berjalan dengan benar
2. **Validasi Data**: Input validation berfungsi dengan baik
3. **Security**: Authorization dan authentication berjalan dengan benar
4. **Performance**: Sistem dapat handle load dengan baik
5. **Data Integrity**: Data konsisten dan tidak corrupt

## 📋 Checklist Testing

### Pre-Testing Checklist

- [ ] Composer dependencies sudah terinstall (`composer install`)
- [ ] MySQL server running
- [ ] Test database `mahkota_test` bisa diakses
- [ ] PHP version >= 7.3
- [ ] Extension PHP yang diperlukan aktif (mysqli, gd, json)

### Testing Checklist

#### Artikel Module
- [ ] Create artikel dengan data valid
- [ ] Create artikel dengan data invalid (missing fields)
- [ ] Read list artikel (empty & with data)
- [ ] Update artikel existing
- [ ] Update artikel non-existing
- [ ] Delete artikel existing
- [ ] Delete artikel non-existing
- [ ] Authorization testing (tanpa session admin)
- [ ] Performance testing (bulk operations)

#### Galeri Module
- [ ] Create galeri dengan gambar
- [ ] Create galeri tanpa gambar (should fail)
- [ ] Read list galeri
- [ ] Update galeri
- [ ] Delete galeri
- [ ] File upload validation
- [ ] Authorization testing

#### Program Module
- [ ] Create program dengan data valid
- [ ] Create program dengan data invalid
- [ ] Read list program
- [ ] Update program
- [ ] Update status progression (planned → ongoing → completed)
- [ ] Delete program
- [ ] Authorization testing
- [ ] Performance testing

#### Integration Testing
- [ ] Complete workflow (Create → Read → Update → Delete)
- [ ] Multiple entities interaction
- [ ] Concurrent operations
- [ ] Data consistency
- [ ] Error handling
- [ ] System load testing

## 🔍 Test Scenarios

### Scenario 1: Happy Path Testing

**Tujuan**: Memastikan flow normal berjalan dengan baik

**Steps**:
1. Create artikel baru dengan data lengkap
2. Verify artikel muncul di list
3. Update artikel
4. Verify perubahan tersimpan
5. Delete artikel
6. Verify artikel terhapus

**Expected Result**: Semua operasi berhasil tanpa error

### Scenario 2: Validation Testing

**Tujuan**: Memastikan validasi input berjalan dengan baik

**Steps**:
1. Create artikel tanpa judul → Should fail
2. Create artikel tanpa kategori → Should fail
3. Create artikel tanpa isi → Should fail
4. Update artikel tanpa ID → Should fail
5. Delete artikel tanpa ID → Should fail

**Expected Result**: Semua operasi gagal dengan error message yang jelas

### Scenario 3: Security Testing

**Tujuan**: Memastikan authorization berjalan dengan baik

**Steps**:
1. Clear admin session
2. Attempt to create artikel → Should fail (Unauthorized)
3. Attempt to read artikel → Should fail (Unauthorized)
4. Attempt to update artikel → Should fail (Unauthorized)
5. Attempt to delete artikel → Should fail (Unauthorized)

**Expected Result**: Semua operasi gagal dengan error "Unauthorized"

### Scenario 4: Performance Testing

**Tujuan**: Memastikan sistem dapat handle load

**Steps**:
1. Create 100 artikel
2. Measure execution time
3. Read all artikel
4. Measure execution time
5. Delete all artikel
6. Measure execution time

**Expected Result**: 
- Create: < 5 seconds
- Read: < 1 second
- Delete: < 5 seconds

### Scenario 5: Edge Cases Testing

**Tujuan**: Test kondisi ekstrim

**Test Cases**:
- Judul artikel sangat panjang (250+ karakter)
- Isi artikel dengan karakter khusus (@#$%^&*())
- Judul dengan HTML tags (<script>alert('XSS')</script>)
- Update artikel yang sudah dihapus
- Delete artikel yang tidak ada
- Concurrent updates pada artikel yang sama

## 📊 Test Report Template

### Test Execution Report

**Date**: [Tanggal Testing]  
**Tester**: [Nama QA Engineer]  
**Environment**: [Development/Staging/Production]  
**Database**: mahkota_test

#### Test Summary

| Module | Total Tests | Passed | Failed | Skipped |
|--------|-------------|--------|--------|---------|
| Artikel | 36 | - | - | - |
| Galeri | 13 | - | - | - |
| Program | 15 | - | - | - |
| Integration | 8 | - | - | - |
| **Total** | **72** | **-** | **-** | **-** |

#### Failed Tests Detail

| Test Case | Module | Error Message | Priority |
|-----------|--------|---------------|----------|
| - | - | - | - |

#### Performance Metrics

| Operation | Records | Execution Time | Status |
|-----------|---------|----------------|--------|
| Create Artikel | 100 | - seconds | - |
| Read Artikel | 100 | - seconds | - |
| Delete Artikel | 100 | - seconds | - |

#### Issues Found

1. **Issue #1**: [Deskripsi issue]
   - **Severity**: Critical/High/Medium/Low
   - **Module**: [Nama module]
   - **Steps to Reproduce**: [Steps]
   - **Expected**: [Expected behavior]
   - **Actual**: [Actual behavior]

#### Recommendations

1. [Rekomendasi 1]
2. [Rekomendasi 2]

#### Sign Off

**QA Engineer**: _______________  
**Date**: _______________

## 🛠️ Debugging Tips

### 1. Test Gagal - Database Connection Error

**Check**:
```bash
# Check MySQL service
# Windows
net start MySQL

# Linux
sudo service mysql status
```

**Fix**: Pastikan MySQL running dan credentials benar di `phpunit.xml`

### 2. Test Gagal - Assertion Error

**Debug**:
```php
// Tambahkan var_dump untuk debug
var_dump($response);
var_dump($result);
exit;
```

**Analyze**: Lihat actual output vs expected output

### 3. Test Timeout

**Possible Causes**:
- Database query terlalu lambat
- Infinite loop dalam code
- Network issue

**Fix**: 
- Optimize database queries
- Add indexes ke table
- Increase timeout limit di phpunit.xml

### 4. Random Test Failures

**Possible Causes**:
- Test tidak isolated (bergantung pada test lain)
- Database tidak di-clean dengan benar
- Race condition

**Fix**:
- Pastikan setiap test independent
- Check setUp() dan tearDown() methods
- Add proper cleanup

## 📈 Metrics & KPIs

### Code Coverage Target

- **Minimum**: 70%
- **Target**: 85%
- **Excellent**: 95%+

### Test Success Rate

- **Acceptable**: 95%
- **Target**: 98%
- **Excellent**: 100%

### Performance Benchmarks

| Operation | Acceptable | Target | Excellent |
|-----------|-----------|--------|-----------|
| Single Create | < 100ms | < 50ms | < 20ms |
| Single Read | < 50ms | < 20ms | < 10ms |
| Single Update | < 100ms | < 50ms | < 20ms |
| Single Delete | < 100ms | < 50ms | < 20ms |
| Bulk Read (100) | < 1s | < 500ms | < 200ms |

## 🔄 Continuous Testing

### Automated Testing Schedule

1. **Pre-Commit**: Run unit tests sebelum commit
2. **Daily**: Run full test suite setiap hari
3. **Pre-Deployment**: Run full test suite + integration tests
4. **Post-Deployment**: Run smoke tests

### CI/CD Integration

```yaml
# Example GitHub Actions workflow
name: Run Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: vendor/bin/phpunit
```

## 📚 Additional Resources

### PHPUnit Documentation
- Official Docs: https://phpunit.de/documentation.html
- Assertions: https://phpunit.de/manual/current/en/appendixes.assertions.html

### Testing Best Practices
- Test Driven Development (TDD)
- Behavior Driven Development (BDD)
- FIRST Principles (Fast, Independent, Repeatable, Self-validating, Timely)

### Tools & Extensions

- **PHPUnit**: Testing framework
- **PHP_CodeCoverage**: Code coverage analysis
- **Mockery**: Mocking framework (optional)
- **Faker**: Generate fake data (optional)

## 💡 Tips untuk QA Engineer

1. **Selalu Test Edge Cases**: Jangan hanya test happy path
2. **Document Everything**: Catat semua findings dan issues
3. **Automate Repetitive Tests**: Gunakan automation untuk test yang repetitive
4. **Think Like a User**: Test dari perspektif end-user
5. **Stay Updated**: Keep up dengan best practices dan tools terbaru
6. **Collaborate**: Komunikasi dengan developers untuk fix issues
7. **Regression Testing**: Selalu run regression tests setelah bug fix

## 🎓 Training Resources

### Beginner Level
- PHPUnit basics
- Database testing
- API testing fundamentals

### Intermediate Level
- Integration testing
- Performance testing
- Security testing

### Advanced Level
- Test automation
- CI/CD integration
- Load testing & stress testing

---

**Happy Testing! 🚀**

Jika ada pertanyaan, silakan hubungi tim development atau lead QA.
