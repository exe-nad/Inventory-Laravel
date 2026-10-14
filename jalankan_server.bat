@echo off
title SweetBakes Laravel Inventory System Server
color 0E

echo =========================================================================
echo    🥐 SweetBakes: Artisan Pastry & Cake Studio Manager
echo    Pertemuan 5: Laravel Fundamental (Arsitektur MVC)
echo    Mahasiswa: Nadya Salsabila Zahra (NIM: 250180031)
echo =========================================================================
echo.
echo Menjalankan Laravel Artisan Development Server di port 8000...
echo Buka browser pada alamat: http://localhost:8000
echo.
echo Tekan CTRL + C untuk menghentikan server.
echo.

"C:\xampp\php\php.exe" artisan serve --port=8000
pause
