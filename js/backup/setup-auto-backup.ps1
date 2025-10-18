# NextCode Group - Otomatik Backup Kurulum Scripti
# Windows Task Scheduler ile günlük otomatik backup

param(
    [string]$BackupTime = "02:00",
    [int]$MaxBackups = 10,
    [switch]$Force = $false
)

# Renkli çıktı için fonksiyonlar
function Write-Success { param($Message) Write-Host $Message -ForegroundColor Green }
function Write-Info { param($Message) Write-Host $Message -ForegroundColor Cyan }
function Write-Warning { param($Message) Write-Host $Message -ForegroundColor Yellow }
function Write-Error { param($Message) Write-Host $Message -ForegroundColor Red }

# Yönetici yetkisi kontrolü
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Error "Bu script yönetici yetkisi ile çalıştırılmalıdır!"
    Write-Info "PowerShell'i yönetici olarak çalıştırın ve tekrar deneyin."
    exit 1
}

Write-Info "🚀 NextCode Group Otomatik Backup Kurulumu Başlatılıyor..."

try {
    # Proje dizinini bul
    $projectDir = Split-Path -Parent $PSScriptRoot
    $backupScript = Join-Path $PSScriptRoot "auto-backup.php"
    
    if (-not (Test-Path $backupScript)) {
        throw "Backup script bulunamadı: $backupScript"
    }
    
    # PHP yolu kontrolü
    $phpPath = Get-Command php -ErrorAction SilentlyContinue
    if (-not $phpPath) {
        Write-Warning "PHP PATH'de bulunamadı. Manuel olarak PHP yolunu belirtin."
        $phpPath = Read-Host "PHP.exe dosyasının tam yolu"
        if (-not (Test-Path $phpPath)) {
            throw "Geçersiz PHP yolu: $phpPath"
        }
    } else {
        $phpPath = $phpPath.Source
    }
    
    Write-Info "📁 Proje Dizini: $projectDir"
    Write-Info "🐘 PHP Yolu: $phpPath"
    Write-Info "⏰ Backup Saati: $BackupTime"
    Write-Info "📦 Maksimum Backup: $MaxBackups"
    
    # Task adı
    $taskName = "NextCode_AutoBackup"
    
    # Mevcut task'ı kontrol et
    $existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
    if ($existingTask -and -not $Force) {
        Write-Warning "Task zaten mevcut: $taskName"
        $overwrite = Read-Host "Üzerine yazmak istiyor musunuz? (y/N)"
        if ($overwrite -ne "y" -and $overwrite -ne "Y") {
            Write-Info "Kurulum iptal edildi."
            exit 0
        }
    }
    
    # Mevcut task'ı sil
    if ($existingTask) {
        Write-Info "🗑️ Mevcut task siliniyor..."
        Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
    }
    
    # Yeni task oluştur
    Write-Info "📋 Yeni task oluşturuluyor..."
    
    # Action oluştur
    $action = New-ScheduledTaskAction -Execute $phpPath -Argument "`"$backupScript`" create" -WorkingDirectory $projectDir
    
    # Trigger oluştur (günlük)
    $trigger = New-ScheduledTaskTrigger -Daily -At $BackupTime
    
    # Settings oluştur
    $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable:$false -ExecutionTimeLimit (New-TimeSpan -Hours 2)
    
    # Principal oluştur (SYSTEM hesabı)
    $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
    
    # Task'ı kaydet
    Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Description "NextCode Group Web Projesi Otomatik Backup"
    
    Write-Success "✅ Otomatik backup task'ı başarıyla oluşturuldu!"
    
    # Task'ı test et
    Write-Info "🧪 Task test ediliyor..."
    Start-ScheduledTask -TaskName $taskName
    Start-Sleep -Seconds 5
    
    $taskInfo = Get-ScheduledTask -TaskName $taskName
    $taskState = $taskInfo.State
    
    if ($taskState -eq "Running") {
        Write-Success "✅ Task başarıyla çalışıyor!"
    } else {
        Write-Warning "⚠️ Task durumu: $taskState"
    }
    
    # Backup klasörünü kontrol et
    $backupDir = Join-Path $projectDir "backup"
    if (Test-Path $backupDir) {
        $backupCount = (Get-ChildItem $backupDir -Directory).Count
        Write-Info "📊 Mevcut backup sayısı: $backupCount"
    }
    
    # Log dosyasını kontrol et
    $logFile = Join-Path $projectDir "logs\backup.log"
    if (Test-Path $logFile) {
        Write-Info "📝 Backup log dosyası: $logFile"
    }
    
    # Özet bilgiler
    Write-Success "🎉 Kurulum tamamlandı!"
    Write-Info "📋 Task Bilgileri:"
    Write-Info "   Ad: $taskName"
    Write-Info "   Saat: $BackupTime (günlük)"
    Write-Info "   PHP: $phpPath"
    Write-Info "   Proje: $projectDir"
    Write-Info "   Maksimum Backup: $MaxBackups"
    
    Write-Info "🔧 Yönetim:"
    Write-Info "   Task'ı görüntülemek için: Get-ScheduledTask -TaskName '$taskName'"
    Write-Info "   Task'ı çalıştırmak için: Start-ScheduledTask -TaskName '$taskName'"
    Write-Info "   Task'ı durdurmak için: Stop-ScheduledTask -TaskName '$taskName'"
    Write-Info "   Task'ı silmek için: Unregister-ScheduledTask -TaskName '$taskName'"
    
    # İlk backup'ı oluştur
    $createFirst = Read-Host "İlk backup'ı şimdi oluşturmak istiyor musunuz? (y/N)"
    if ($createFirst -eq "y" -or $createFirst -eq "Y") {
        Write-Info "🔄 İlk backup oluşturuluyor..."
        & $phpPath $backupScript create
        Write-Success "✅ İlk backup oluşturuldu!"
    }
    
} catch {
    Write-Error "❌ Kurulum sırasında hata oluştu: $($_.Exception.Message)"
    Write-Error "Stack Trace: $($_.ScriptStackTrace)"
    exit 1
}

Write-Success "✨ Otomatik backup sistemi başarıyla kuruldu!"
Write-Info "📅 İlk otomatik backup: $BackupTime (yarın)"
Write-Info "📊 Backup'ları yönetmek için admin panelini kullanabilirsiniz."
