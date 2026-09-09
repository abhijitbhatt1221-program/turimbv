@echo off
echo Starting TOURIM Local Preview Server...
echo Preview URL: http://localhost:8000
echo Press Ctrl+C to stop.
echo.
php -S localhost:8000 router.php
pause
