@echo off
REM NextCode Log Cleaner - Windows Task Scheduler Setup
REM Bu script log temizleyiciyi otomatik çalıştırmak için Windows görevini kurar

echo NextCode Log Cleaner - Windows Task Scheduler Kurulumu
echo =====================================================
echo.

REM Mevcut dizini al
set "CURRENT_DIR=%~dp0"
set "PHP_PATH=php.exe"
set "LOG_CLEANER=%CURRENT_DIR%log_cleaner.php"

REM PHP yolunu kontrol et
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo HATA: PHP bulunamadi! Lutfen PHP'yi PATH'e ekleyin.
    echo.
    echo PHP'yi manuel olarak kurmak icin:
    echo 1. https://www.php.net/downloads.php adresinden PHP indirin
    echo 2. PATH ortam degiskenine PHP klasorunu ekleyin
    echo.
    pause
    exit /b 1
)

echo PHP bulundu: %PHP_PATH%
echo Log Cleaner: %LOG_CLEANER%
echo.

REM Test çalıştırma
echo Log Cleaner test ediliyor...
%PHP_PATH% %LOG_CLEANER%
if %errorlevel% neq 0 (
    echo HATA: Log Cleaner test basarisiz!
    pause
    exit /b 1
)

echo.
echo Test basarili! Windows gorevini kurmak icin devam ediliyor...
echo.

REM Admin yetkileri kontrol et
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo UYARI: Admin yetkileri gerekli. Bu script'i "Yonetici olarak calistir" ile baslatin.
    echo.
    pause
    exit /b 1
)

REM Görev adı
set "TASK_NAME=NextCode_Log_Cleaner"

REM Mevcut görevi sil (varsa)
schtasks /delete /tn "%TASK_NAME%" /f >nul 2>&1

REM Yeni görev oluştur (günlük, gece 2:00'da)
echo Windows gorevi olusturuluyor...
schtasks /create ^
    /tn "%TASK_NAME%" ^
    /tr "\"%PHP_PATH%\" \"%LOG_CLEANER%\"" ^
    /sc daily ^
    /st 02:00 ^
    /ru "SYSTEM" ^
    /f

if %errorlevel% equ 0 (
    echo.
    echo ==========================================
    echo BASARILI! Log Cleaner otomatik kuruldu.
    echo ==========================================
    echo.
    echo Gorev Adi: %TASK_NAME%
    echo Calisma Zamani: Her gun gece 02:00
    echo PHP Yolu: %PHP_PATH%
    echo Script Yolu: %LOG_CLEANER%
    echo.
    echo Manuel calistirmak icin:
    echo   schtasks /run /tn "%TASK_NAME%"
    echo.
    echo Gorevi silmek icin:
    echo   schtasks /delete /tn "%TASK_NAME%" /f
    echo.
    echo Gorev durumunu kontrol etmek icin:
    echo   schtasks /query /tn "%TASK_NAME%"
    echo.
) else (
    echo.
    echo HATA: Windows gorevi olusturulamadi!
    echo Lutfen admin yetkileri ile tekrar deneyin.
    echo.
)

echo Log dosyalari su anda:
%PHP_PATH% %LOG_CLEANER%
echo.

pause

