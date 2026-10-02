@echo off
rem Serves the site locally at http://localhost:8000
rem Download counts and subscriber emails go to dev-data, never to public\database, which gets uploaded
if not exist "%~dp0dev-data" mkdir "%~dp0dev-data"
set "LIMA_DATA_DIR=%~dp0dev-data"
php -S localhost:8000 -t "%~dp0public"
