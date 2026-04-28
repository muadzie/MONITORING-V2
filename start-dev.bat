@echo off
title Monitoring PKL Development

echo ========================================
echo  Starting Monitoring PKL Development
echo ========================================
echo.

echo [1/4] Clearing Vite cache...
cd C:\laragon\www\MONITORING-V2\monitoring-pkl-frontend
rmdir /s /q node_modules\.vite 2>nul
rmdir /s /q .vite 2>nul

echo [2/4] Clearing Laravel cache...
cd C:\laragon\www\MONITORING-V2\monitoring-pkl-backend
php artisan optimize:clear

echo [3/4] Starting Backend Server...
start "Backend Server" cmd /k "cd C:\laragon\www\MONITORING-V2\monitoring-pkl-backend && php artisan serve"

timeout /t 3

echo [4/4] Starting Frontend Server...
start "Frontend Server" cmd /k "cd C:\laragon\www\MONITORING-V2\monitoring-pkl-frontend && npm run dev"

echo.
echo ========================================
echo  Servers are running!
echo  Backend:  http://localhost:8000
echo  Frontend: http://localhost:5173
echo ========================================
echo.
echo Tips:
echo - Buka Chrome dengan: chrome.exe --disable-cache
echo - Atau gunakan Incognito Mode (Ctrl+Shift+N)
echo - Tekan Ctrl+C di jendela server untuk menghentikan
echo ========================================
pause