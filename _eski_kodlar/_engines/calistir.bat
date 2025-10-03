@echo off
set arg1=%1
title Send SMS %1
:loop
php.exe -c php.ini c:\xxx.php %arg1%
goto loop
REM ping 127.0.0.1 -n 2 > nul
