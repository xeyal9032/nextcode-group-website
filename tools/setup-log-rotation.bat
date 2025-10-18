@echo off
REM NextCode Group - Log Rotation Setup Script
REM Windows Task Scheduler için log rotasyon kurulumu

echo NextCode Group - Log Rotation Setup
echo ====================================

REM Mevcut dizini kontrol et
if not exist "log-rotation.php" (
    echo HATA: log-rotation.php dosyasi bulunamadi!
    echo Lutfen bu scripti tools klasorunde calistirin.
    pause
    exit /b 1
)

REM PHP'nin yüklü olup olmadığını kontrol et
php --version >nul 2>&1
if errorlevel 1 (
    echo HATA: PHP yuklu degil veya PATH'te degil!
    echo Lutfen PHP'yi yukleyin ve PATH'e ekleyin.
    pause
    exit /b 1
)

echo PHP bulundu: 
php --version

REM Log rotasyon testi
echo.
echo Log rotasyon sistemi test ediliyor...
php log-rotation.php stats

REM Task Scheduler XML oluştur
echo.
echo Task Scheduler XML dosyasi olusturuluyor...

set "SCRIPT_DIR=%~dp0"
set "PHP_PATH=php"
set "LOG_ROTATION_SCRIPT=%SCRIPT_DIR%log-rotation.php"

REM XML dosyasını oluştur
(
echo ^<?xml version="1.0" encoding="UTF-16"?^>
echo ^<Task version="1.2" xmlns="http://schemas.microsoft.com/windows/2004/02/mit/task"^>
echo   ^<Triggers^>
echo     ^<CalendarTrigger^>
echo       ^<StartBoundary^>%date%T02:00:00^</StartBoundary^>
echo       ^<Enabled^>true^</Enabled^>
echo       ^<ScheduleByDay^>
echo         ^<DaysInterval^>1^</DaysInterval^>
echo       ^</ScheduleByDay^>
echo     ^</CalendarTrigger^>
echo   ^</Triggers^>
echo   ^<Principals^>
echo     ^<Principal id="Author"^>
echo       ^<UserId^>S-1-5-18^</UserId^>
echo       ^<RunLevel^>LeastPrivilege^</RunLevel^>
echo     ^</Principal^>
echo   ^</Principals^>
echo   ^<Settings^>
echo     ^<MultipleInstancesPolicy^>IgnoreNew^</MultipleInstancesPolicy^>
echo     ^<DisallowStartIfOnBatteries^>false^</DisallowStartIfOnBatteries^>
echo     ^<StopIfGoingOnBatteries^>false^</StopIfGoingOnBatteries^>
echo     ^<AllowHardTerminate^>true^</AllowHardTerminate^>
echo     ^<StartWhenAvailable^>true^</StartWhenAvailable^>
echo     ^<RunOnlyIfNetworkAvailable^>false^</RunOnlyIfNetworkAvailable^>
echo     ^<IdleSettings^>
echo       ^<StopOnIdleEnd^>true^</StopOnIdleEnd^>
echo       ^<RestartOnIdle^>false^</RestartOnIdle^>
echo     ^</IdleSettings^>
echo     ^<AllowStartOnDemand^>true^</AllowStartOnDemand^>
echo     ^<Enabled^>true^</Enabled^>
echo     ^<Hidden^>false^</Hidden^>
echo     ^<RunOnlyIfIdle^>false^</RunOnlyIfIdle^>
echo     ^<WakeToRun^>false^</WakeToRun^>
echo     ^<ExecutionTimeLimit^>PT30M^</ExecutionTimeLimit^>
echo     ^<Priority^>7^</Priority^>
echo   ^</Settings^>
echo   ^<Actions^>
echo     ^<Exec^>
echo       ^<Command^>%PHP_PATH%^</Command^>
echo       ^<Arguments^>"%LOG_ROTATION_SCRIPT%" rotate^</Arguments^>
echo       ^<WorkingDirectory^>%SCRIPT_DIR%^</WorkingDirectory^>
echo     ^</Exec^>
echo   ^</Actions^>
echo ^</Task^>
) > "NextCode_LogRotation.xml"

echo Task Scheduler XML dosyasi olusturuldu: NextCode_LogRotation.xml

REM Task'ı kaydetme talimatları
echo.
echo ====================================
echo KURULUM TALIMATLARI:
echo ====================================
echo 1. Task Scheduler'i acin (taskschd.msc)
echo 2. "Create Task" tiklayin
echo 3. "Import Task" butonuna tiklayin
echo 4. NextCode_LogRotation.xml dosyasini secin
echo 5. Task'i kaydedin
echo.
echo VEYA
echo.
echo Komut satirindan:
echo schtasks /create /xml "NextCode_LogRotation.xml" /tn "NextCode_LogRotation"
echo.

REM Manuel test
echo Manuel test yapmak ister misiniz? (E/H)
set /p choice=
if /i "%choice%"=="E" (
    echo.
    echo Log rotasyon testi calistiriliyor...
    php log-rotation.php rotate
    echo.
    echo Test tamamlandi!
)

echo.
echo Kurulum tamamlandi!
pause

