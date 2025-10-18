# NextCode Group Web Projesi - Restore Scripti
# PowerShell Script - Backup'tan geri yükleme

param(
    [Parameter(Mandatory=$true)]
    [string]$BackupPath,
    [string]$TargetPath = ".",
    [switch]$Force = $false
)

# Renkli çıktı için fonksiyonlar
function Write-Success { param($Message) Write-Host $Message -ForegroundColor Green }
function Write-Info { param($Message) Write-Host $Message -ForegroundColor Cyan }
function Write-Warning { param($Message) Write-Host $Message -ForegroundColor Yellow }
function Write-Error { param($Message) Write-Host $Message -ForegroundColor Red }

Write-Info "🔄 NextCode Group Web Projesi Restore Başlatılıyor..."
Write-Info "📁 Backup Klasörü: $BackupPath"
Write-Info "🎯 Hedef Klasör: $TargetPath"

# Backup klasörünün var olup olmadığını kontrol et
if (-not (Test-Path $BackupPath)) {
    Write-Error "❌ Backup klasörü bulunamadı: $BackupPath"
    exit 1
}

# Backup bilgilerini oku
$backupInfoFile = Join-Path $BackupPath "backup-info.json"
if (Test-Path $backupInfoFile) {
    $backupInfo = Get-Content $backupInfoFile | ConvertFrom-Json
    Write-Info "📋 Backup Bilgileri:"
    Write-Info "   📅 Tarih: $($backupInfo.date)"
    Write-Info "   📄 Dosya Sayısı: $($backupInfo.files_count)"
    Write-Info "   💾 Boyut: $($backupInfo.size_mb) MB"
} else {
    Write-Warning "⚠️ Backup bilgi dosyası bulunamadı"
}

# Hedef klasörün var olup olmadığını kontrol et
if (-not (Test-Path $TargetPath)) {
    Write-Info "📂 Hedef klasör oluşturuluyor: $TargetPath"
    New-Item -ItemType Directory -Path $TargetPath -Force | Out-Null
}

# Mevcut dosyaları kontrol et
$existingFiles = Get-ChildItem $TargetPath -File | Where-Object { $_.Name -match "\.(php|css|js|sql)$" }
if ($existingFiles.Count -gt 0 -and -not $Force) {
    Write-Warning "⚠️ Hedef klasörde mevcut dosyalar bulundu!"
    Write-Warning "   Mevcut dosya sayısı: $($existingFiles.Count)"
    Write-Warning "   Devam etmek için -Force parametresini kullanın"
    exit 1
}

try {
    # PHP dosyalarını geri yükle
    Write-Info "📄 PHP dosyaları geri yükleniyor..."
    $phpSource = Join-Path $BackupPath "php"
    if (Test-Path $phpSource) {
        Copy-Item "$phpSource\*" $TargetPath -Force
        Write-Success "✅ PHP dosyaları geri yüklendi"
    }
    
    # CSS dosyalarını geri yükle
    Write-Info "🎨 CSS dosyaları geri yükleniyor..."
    $cssSource = Join-Path $BackupPath "css"
    $cssTarget = Join-Path $TargetPath "css"
    if (Test-Path $cssSource) {
        if (-not (Test-Path $cssTarget)) {
            New-Item -ItemType Directory -Path $cssTarget -Force | Out-Null
        }
        Copy-Item "$cssSource\*" $cssTarget -Recurse -Force
        Write-Success "✅ CSS dosyaları geri yüklendi"
    }
    
    # JavaScript dosyalarını geri yükle
    Write-Info "⚡ JavaScript dosyaları geri yükleniyor..."
    $jsSource = Join-Path $BackupPath "js"
    $jsTarget = Join-Path $TargetPath "js"
    if (Test-Path $jsSource) {
        if (-not (Test-Path $jsTarget)) {
            New-Item -ItemType Directory -Path $jsTarget -Force | Out-Null
        }
        Copy-Item "$jsSource\*" $jsTarget -Recurse -Force
        Write-Success "✅ JavaScript dosyaları geri yüklendi"
    }
    
    # Config dosyalarını geri yükle
    Write-Info "⚙️ Konfigürasyon dosyaları geri yükleniyor..."
    $configSource = Join-Path $BackupPath "config"
    $configTarget = Join-Path $TargetPath "config"
    if (Test-Path $configSource) {
        if (-not (Test-Path $configTarget)) {
            New-Item -ItemType Directory -Path $configTarget -Force | Out-Null
        }
        Copy-Item "$configSource\*" $configTarget -Recurse -Force
        Write-Success "✅ Konfigürasyon dosyaları geri yüklendi"
    }
    
    # Database dosyalarını geri yükle
    Write-Info "🗄️ Veritabanı dosyaları geri yükleniyor..."
    $dbSource = Join-Path $BackupPath "database"
    $dbTarget = Join-Path $TargetPath "database"
    if (Test-Path $dbSource) {
        if (-not (Test-Path $dbTarget)) {
            New-Item -ItemType Directory -Path $dbTarget -Force | Out-Null
        }
        Copy-Item "$dbSource\*" $dbTarget -Recurse -Force
        Write-Success "✅ Veritabanı dosyaları geri yüklendi"
    }
    
    # Images klasörünü geri yükle
    Write-Info "🖼️ Görsel dosyaları geri yükleniyor..."
    $imagesSource = Join-Path $BackupPath "images"
    $imagesTarget = Join-Path $TargetPath "images"
    if (Test-Path $imagesSource) {
        if (-not (Test-Path $imagesTarget)) {
            New-Item -ItemType Directory -Path $imagesTarget -Force | Out-Null
        }
        Copy-Item "$imagesSource\*" $imagesTarget -Recurse -Force
        Write-Success "✅ Görsel dosyaları geri yüklendi"
    }
    
    # Admin klasörünü geri yükle
    Write-Info "👨‍💼 Admin dosyaları geri yükleniyor..."
    $adminSource = Join-Path $BackupPath "admin"
    $adminTarget = Join-Path $TargetPath "admin"
    if (Test-Path $adminSource) {
        if (-not (Test-Path $adminTarget)) {
            New-Item -ItemType Directory -Path $adminTarget -Force | Out-Null
        }
        Copy-Item "$adminSource\*" $adminTarget -Recurse -Force
        Write-Success "✅ Admin dosyaları geri yüklendi"
    }
    
    # API klasörünü geri yükle
    Write-Info "🔌 API dosyaları geri yükleniyor..."
    $apiSource = Join-Path $BackupPath "api"
    $apiTarget = Join-Path $TargetPath "api"
    if (Test-Path $apiSource) {
        if (-not (Test-Path $apiTarget)) {
            New-Item -ItemType Directory -Path $apiTarget -Force | Out-Null
        }
        Copy-Item "$apiSource\*" $apiTarget -Recurse -Force
        Write-Success "✅ API dosyaları geri yüklendi"
    }
    
    # Includes klasörünü geri yükle
    Write-Info "📚 Include dosyaları geri yükleniyor..."
    $includesSource = Join-Path $BackupPath "includes"
    $includesTarget = Join-Path $TargetPath "includes"
    if (Test-Path $includesSource) {
        if (-not (Test-Path $includesTarget)) {
            New-Item -ItemType Directory -Path $includesTarget -Force | Out-Null
        }
        Copy-Item "$includesSource\*" $includesTarget -Recurse -Force
        Write-Success "✅ Include dosyaları geri yüklendi"
    }
    
    # Documentation klasörünü geri yükle
    Write-Info "📖 Dokümantasyon dosyaları geri yükleniyor..."
    $docSource = Join-Path $BackupPath "documentation"
    $docTarget = Join-Path $TargetPath "documentation"
    if (Test-Path $docSource) {
        if (-not (Test-Path $docTarget)) {
            New-Item -ItemType Directory -Path $docTarget -Force | Out-Null
        }
        Copy-Item "$docSource\*" $docTarget -Recurse -Force
        Write-Success "✅ Dokümantasyon dosyaları geri yüklendi"
    }
    
    # Diğer dosyaları geri yükle
    Write-Info "📋 Diğer dosyalar geri yükleniyor..."
    $otherFiles = @("*.md", "*.txt", "*.sql", "*.htaccess", "router.php", "api-router.php")
    foreach ($pattern in $otherFiles) {
        $files = Get-ChildItem $BackupPath -Name $pattern -File
        foreach ($file in $files) {
            Copy-Item (Join-Path $BackupPath $file) $TargetPath -Force
        }
    }
    Write-Success "✅ Diğer dosyalar geri yüklendi"
    
    # Restore özeti
    $restoredFiles = (Get-ChildItem $TargetPath -Recurse -File).Count
    Write-Success "🎉 Restore başarıyla tamamlandı!"
    Write-Info "📊 Restore Özeti:"
    Write-Info "   📁 Hedef: $TargetPath"
    Write-Info "   📄 Geri Yüklenen Dosya Sayısı: $restoredFiles"
    Write-Info "   📅 Tarih: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
    
    Write-Info "🔧 Sonraki Adımlar:"
    Write-Info "   1. Veritabanı bağlantı bilgilerini kontrol edin"
    Write-Info "   2. Dosya izinlerini ayarlayın"
    Write-Info "   3. Web sunucusunu yeniden başlatın"
    Write-Info "   4. Site erişimini test edin"
    
    Write-Success "✨ Restore işlemi tamamlandı!"
    
} catch {
    Write-Error "❌ Restore sırasında hata oluştu: $($_.Exception.Message)"
    exit 1
}
