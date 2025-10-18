# NextCode Group Web Projesi - Otomatik Backup Scripti
# PowerShell Script - Windows için optimize edilmiş

param(
    [string]$BackupPath = "backup",
    [switch]$Compress = $false,
    [switch]$IncludeDatabase = $true
)

# Renkli çıktı için fonksiyonlar
function Write-Success { param($Message) Write-Host $Message -ForegroundColor Green }
function Write-Info { param($Message) Write-Host $Message -ForegroundColor Cyan }
function Write-Warning { param($Message) Write-Host $Message -ForegroundColor Yellow }
function Write-Error { param($Message) Write-Host $Message -ForegroundColor Red }

# Tarih damgası oluştur
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
$backupDir = Join-Path $BackupPath $timestamp

Write-Info "🚀 NextCode Group Web Projesi Backup Başlatılıyor..."
Write-Info "📅 Backup Tarihi: $timestamp"
Write-Info "📁 Backup Klasörü: $backupDir"

try {
    # Backup klasörünü oluştur
    Write-Info "📂 Backup klasör yapısı oluşturuluyor..."
    $folders = @("php", "css", "js", "config", "database", "images", "api", "includes", "documentation")
    
    foreach ($folder in $folders) {
        $folderPath = Join-Path $backupDir $folder
        New-Item -ItemType Directory -Path $folderPath -Force | Out-Null
        Write-Success "✅ $folder klasörü oluşturuldu"
    }
    
    # Ana PHP dosyalarını kopyala
    Write-Info "📄 PHP dosyaları kopyalanıyor..."
    Copy-Item "*.php" (Join-Path $backupDir "php") -Force
    Write-Success "✅ Ana PHP dosyaları kopyalandı"
    
    # CSS dosyalarını kopyala
    Write-Info "🎨 CSS dosyaları kopyalanıyor..."
    Copy-Item "css\*" (Join-Path $backupDir "css") -Recurse -Force
    Write-Success "✅ CSS dosyaları kopyalandı"
    
    # JavaScript dosyalarını kopyala
    Write-Info "⚡ JavaScript dosyaları kopyalanıyor..."
    Copy-Item "js\*" (Join-Path $backupDir "js") -Recurse -Force
    Write-Success "✅ JavaScript dosyaları kopyalandı"
    
    # Config dosyalarını kopyala
    Write-Info "⚙️ Konfigürasyon dosyaları kopyalanıyor..."
    Copy-Item "config\*" (Join-Path $backupDir "config") -Recurse -Force
    Write-Success "✅ Konfigürasyon dosyaları kopyalandı"
    
    # Database dosyalarını kopyala
    if ($IncludeDatabase) {
        Write-Info "🗄️ Veritabanı dosyaları kopyalanıyor..."
        Copy-Item "database\*" (Join-Path $backupDir "database") -Recurse -Force
        Write-Success "✅ Veritabanı dosyaları kopyalandı"
    }
    
    # Images klasörünü kopyala
    Write-Info "🖼️ Görsel dosyaları kopyalanıyor..."
    Copy-Item "images\*" (Join-Path $backupDir "images") -Recurse -Force
    Write-Success "✅ Görsel dosyaları kopyalandı"
    
    
    # API klasörünü kopyala
    Write-Info "🔌 API dosyaları kopyalanıyor..."
    Copy-Item "api\*" (Join-Path $backupDir "api") -Recurse -Force
    Write-Success "✅ API dosyaları kopyalandı"
    
    # Includes klasörünü kopyala
    Write-Info "📚 Include dosyaları kopyalanıyor..."
    Copy-Item "includes\*" (Join-Path $backupDir "includes") -Recurse -Force
    Write-Success "✅ Include dosyaları kopyalandı"
    
    # Documentation klasörünü kopyala
    Write-Info "📖 Dokümantasyon dosyaları kopyalanıyor..."
    Copy-Item "documentation\*" (Join-Path $backupDir "documentation") -Recurse -Force
    Write-Success "✅ Dokümantasyon dosyaları kopyalandı"
    
    # Diğer önemli dosyaları kopyala
    Write-Info "📋 Diğer önemli dosyalar kopyalanıyor..."
    $otherFiles = @("*.md", "*.txt", "*.sql", "*.htaccess", "router.php", "api-router.php")
    foreach ($pattern in $otherFiles) {
        if (Test-Path $pattern) {
            Copy-Item $pattern $backupDir -Force
        }
    }
    Write-Success "✅ Diğer dosyalar kopyalandı"
    
    # Backup bilgilerini kaydet
    $backupInfo = @{
        "timestamp" = $timestamp
        "date" = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        "files_count" = (Get-ChildItem $backupDir -Recurse -File).Count
        "size_mb" = [math]::Round(((Get-ChildItem $backupDir -Recurse -File | Measure-Object -Property Length -Sum).Sum / 1MB), 2)
        "include_database" = $IncludeDatabase
        "compressed" = $Compress
    }
    
    $backupInfo | ConvertTo-Json | Out-File (Join-Path $backupDir "backup-info.json") -Encoding UTF8
    
    # Backup özeti
    Write-Success "🎉 Backup başarıyla tamamlandı!"
    Write-Info "📊 Backup Özeti:"
    Write-Info "   📁 Klasör: $backupDir"
    Write-Info "   📄 Dosya Sayısı: $($backupInfo.files_count)"
    Write-Info "   💾 Boyut: $($backupInfo.size_mb) MB"
    Write-Info "   📅 Tarih: $($backupInfo.date)"
    
    # Eski backup'ları temizle (30 günden eski)
    Write-Info "🧹 Eski backup'lar temizleniyor..."
    $oldBackups = Get-ChildItem $BackupPath -Directory | Where-Object { 
        $_.CreationTime -lt (Get-Date).AddDays(-30) 
    }
    
    if ($oldBackups.Count -gt 0) {
        $oldBackups | Remove-Item -Recurse -Force
        Write-Success "✅ $($oldBackups.Count) eski backup silindi"
    } else {
        Write-Info "ℹ️ Silinecek eski backup bulunamadı"
    }
    
    Write-Success "✨ Backup işlemi tamamlandı!"
    
} catch {
    Write-Error "❌ Backup sırasında hata oluştu: $($_.Exception.Message)"
    exit 1
}
