#!/bin/bash

# Jalankan package discover terlebih dahulu
php artisan package:discover --ansi

# Tunggu database mysql siap
echo "Menunggu MySQL..."
while ! php artisan db:show > /dev/null 2>&1; do
  sleep 2
done

# Jalankan migrasi dan seeder jika belum ada
echo "Menjalankan migrasi database..."
php artisan migrate --force

# Seed database dengan master data (hanya jalan jika tabel prodis kosong, atau kita bisa force dengan logika tertentu)
# Kita jalankan artisan db:seed tapi pastikan seeder idempotent atau kita abaikan jika sudah ada.
# Untuk demo, ini akan populate. Jika database persisten, hati-hati duplikasi seeder.
php artisan db:seed --force || true

# Jalankan cache config dsb
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Perbaiki izin setelah artisan command membuat file cache/log
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Jalankan perintah utama (CMD)
exec "$@"
