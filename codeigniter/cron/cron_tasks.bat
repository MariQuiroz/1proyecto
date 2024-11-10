@echo off
SET XAMPP_PHP=C:\xampp\php\php.exe
SET PROJECT_PATH=%~dp0..
SET LOG_PATH=%PROJECT_PATH%\logs\cron

:: Crear directorio de logs si no existe
if not exist "%LOG_PATH%" mkdir "%LOG_PATH%"

:: Establecer la fecha actual
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set TIMESTAMP=%datetime:~0,4%-%datetime:~4,2%-%datetime:~6,2% %datetime:~8,2%:%datetime:~10,2%:%datetime:~12,2%

:: Ejecutar el script PHP
echo [%TIMESTAMP%] Iniciando tareas programadas >> "%LOG_PATH%\cron.log"
"%XAMPP_PHP%" "%PROJECT_PATH%\cron\cron_tasks.php" >> "%LOG_PATH%\cron.log" 2>&1
echo [%TIMESTAMP%] Finalizando tareas programadas >> "%LOG_PATH%\cron.log"