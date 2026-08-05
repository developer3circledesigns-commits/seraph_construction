@echo off
rem ============================================================
rem  SERAPH Construction - local dev server
rem
rem  Runs the project under XAMPP Apache (multi-threaded/mod_php)
rem  instead of `php -S`. The PHP built-in server on Windows is
rem  single-threaded: a realtime SSE stream holds its only worker
rem  and blocks every other request, which shows up as constant
rem  "Reconnecting..." in the admin/client panels.
rem
rem  This vhost is defined in:
rem    C:\xampp\apache\conf\extra\httpd-vhosts.conf
rem  URL:  http://127.0.0.1:8080/
rem
rem  Usage:  .\start-dev-server.cmd   (from PowerShell)
rem ============================================================
setlocal
cd /d "%~dp0"

set HTTPD=C:\xampp\apache\bin\httpd.exe

if not exist "%HTTPD%" (
  echo ERROR: Apache not found at %HTTPD%
  exit /b 1
)

rem If Apache is already serving the project, nothing to do.
powershell -NoProfile -Command "try { $r = Invoke-WebRequest -Uri 'http://127.0.0.1:8080/admin/login.php' -UseBasicParsing -TimeoutSec 4; exit 0 } catch { exit 1 }"
if %errorlevel% equ 0 (
  echo Seraph dev server is already running on http://127.0.0.1:8080/
  echo (Apache %HTTPD%)
  goto :show
)

rem Stop a leftover single-threaded php -S server if still running
taskkill /IM php.exe /F >nul 2>&1

echo Starting Seraph dev server via Apache on http://127.0.0.1:8080 ...
start "Seraph Apache" "%HTTPD%" -D FOREGROUND
powershell -NoProfile -Command "Start-Sleep -Seconds 4"

:show
echo.
echo URL:   http://127.0.0.1:8080/
echo Admin: http://127.0.0.1:8080/admin/login.php
echo Restart Apache with:  taskkill /IM httpd.exe /F   then re-run this script