# NextCode Project Organization Script
# Ana dizini temizleyip profesyonel hale getirir

Write-Host "`n============================================" -ForegroundColor Cyan
Write-Host "NextCode Project Organization" -ForegroundColor Cyan
Write-Host "============================================`n" -ForegroundColor Cyan

$organized = 0
$skipped = 0
$errors = 0

# 1. MD dosyalarını docs/ klasörüne taşı (README.md hariç)
Write-Host "[1/7] MD dosyalarını organize ediliyor..." -ForegroundColor Yellow

$mdFiles = Get-ChildItem -Filter "*.md" | Where-Object { $_.Name -ne "README.md" }
foreach ($file in $mdFiles) {
    try {
        Move-Item $file.FullName "docs/$($file.Name)" -Force -ErrorAction Stop
        Write-Host "  ✓ $($file.Name) -> docs/" -ForegroundColor Green
        $organized++
    } catch {
        Write-Host "  ✗ $($file.Name) taşınamadı" -ForegroundColor Red
        $errors++
    }
}

# 2. TXT dosyalarını docs/ klasörüne taşı (robots.txt hariç)
Write-Host "`n[2/7] TXT dosyalarını organize ediliyor..." -ForegroundColor Yellow

$txtFiles = Get-ChildItem -Filter "*.txt" | Where-Object { $_.Name -ne "robots.txt" }
foreach ($file in $txtFiles) {
    try {
        Move-Item $file.FullName "docs/$($file.Name)" -Force -ErrorAction Stop
        Write-Host "  ✓ $($file.Name) -> docs/" -ForegroundColor Green
        $organized++
    } catch {
        Write-Host "  ✗ $($file.Name) taşınamadı" -ForegroundColor Red
        $errors++
    }
}

# 3. Verification HTML dosyalarını docs/verification/ klasörüne taşı
Write-Host "`n[3/7] HTML verification dosyalarını organize ediliyor..." -ForegroundColor Yellow

if (-not (Test-Path "docs/verification")) {
    New-Item -ItemType Directory -Path "docs/verification" | Out-Null
}

$htmlFiles = @(
    "analytics-verification.html",
    "google-search-console-setup.html",
    "google6b45980adf7adfc5.html"
)

foreach ($fileName in $htmlFiles) {
    if (Test-Path $fileName) {
        try {
            Move-Item $fileName "docs/verification/$fileName" -Force -ErrorAction Stop
            Write-Host "  ✓ $fileName -> docs/verification/" -ForegroundColor Green
            $organized++
        } catch {
            Write-Host "  ✗ $fileName taşınamadı" -ForegroundColor Red
            $errors++
        }
    }
}

# 4. Test PHP dosyalarını tests/ klasörüne taşı
Write-Host "`n[4/7] Test PHP dosyalarını organize ediliyor..." -ForegroundColor Yellow

$testFiles = @(
    "analytics-test.php",
    "ajax-demo.php",
    "check-database-compatibility.php",
    "mysql-client.php",
    "mysql-client-advanced.php",
    "mysql-client-helper.php",
    "veritabani-kontrol.php",
    "veritabani-guncelle.php"
)

foreach ($fileName in $testFiles) {
    if (Test-Path $fileName) {
        try {
            Move-Item $fileName "tests/$fileName" -Force -ErrorAction Stop
            Write-Host "  ✓ $fileName -> tests/" -ForegroundColor Green
            $organized++
        } catch {
            Write-Host "  ✗ $fileName taşınamadı" -ForegroundColor Red
            $errors++
        }
    }
}

# 5. SQL dosyalarını database/ klasörüne taşı
Write-Host "`n[5/7] SQL dosyalarını organize ediliyor..." -ForegroundColor Yellow

$sqlFiles = Get-ChildItem -Filter "*.sql"
foreach ($file in $sqlFiles) {
    try {
        Move-Item $file.FullName "database/$($file.Name)" -Force -ErrorAction Stop
        Write-Host "  ✓ $($file.Name) -> database/" -ForegroundColor Green
        $organized++
    } catch {
        Write-Host "  ✗ $($file.Name) taşınamadı" -ForegroundColor Red
        $errors++
    }
}

# 6. Backup klasörünü temizle/taşı
Write-Host "`n[6/7] Backup klasörünü kontrol ediliyor..." -ForegroundColor Yellow

if (Test-Path "backup") {
    $backupSize = (Get-ChildItem "backup" -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB
    Write-Host "  ℹ Backup klasörü: $([math]::Round($backupSize, 2)) MB" -ForegroundColor Cyan
    Write-Host "  ! Backup klasörü korundu (manuel kontrol gerekli)" -ForegroundColor Yellow
    $skipped++
}

# 7. Composer ve NPM dosyalarını kontrol et
Write-Host "`n[7/7] Package yönetimi dosyaları..." -ForegroundColor Yellow

$packageFiles = @("composer.json", "composer.lock", "package.json", "package-lock.json")
foreach ($fileName in $packageFiles) {
    if (Test-Path $fileName) {
        Write-Host "  ✓ $fileName -> ROOT (doğru yerde)" -ForegroundColor Green
    }
}

# Özet
Write-Host "`n============================================" -ForegroundColor Cyan
Write-Host "ORGANIZASYON TAMAMLANDI" -ForegroundColor Cyan
Write-Host "============================================`n" -ForegroundColor Cyan

Write-Host "Taşınan dosyalar: $organized" -ForegroundColor Green
Write-Host "Hata: $errors" -ForegroundColor Red
Write-Host "Atlanan: $skipped`n" -ForegroundColor Yellow

Write-Host "✅ Ana dizin temizlendi ve profesyonel görünüm kazandı!`n" -ForegroundColor Green


