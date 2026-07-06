@echo off
title Motor de Sincronizacion Biometrica ZKTeco
color 0a
echo ========================================================
echo   MANTEN ESTA VENTANA ABIERTA (MINIMIZADA)
echo   El sistema esta sincronizando en tiempo real...
echo ========================================================
cd c:\xampp\htdocs\login_register12\controlador
php -f demonio_biometrico.php
pause
