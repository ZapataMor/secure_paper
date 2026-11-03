@echo off
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0tools\local\server.ps1" -Accion detener
pause
