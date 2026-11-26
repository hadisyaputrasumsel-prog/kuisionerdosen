#!/bin/bash
# Remove leftover Apache PID to avoid crash on container restart
rm -f /var/run/apache2/apache2.pid

# Ensure storage and upload directories exist
mkdir -p /var/www/html/public/uploads \
         /var/www/html/storage/app/public \
         /var/www/html/storage/app/private \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs

# Run package discover
echo "Menjalankan package:discover..."
php artisan package:discover --ansi || true

# Ensure storage symlink exists
php artisan storage:link --force || true

# Wait for database
echo "Menunggu MySQL..."
max_tries=30
count=0
db_ready=0
while [ $count -lt $max_tries ]; do
    if php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Throwable \$e) { exit(1); }"; then
        echo "Database siap!"
        db_ready=1
        break
    fi
    count=$((count + 1))
    echo "Menunggu koneksi database ($count/$max_tries)..."
    sleep 2
done

if [ $db_ready -eq 1 ]; then
    echo "Menjalankan migrasi database..."
    php artisan migrate --force || true

    echo "Menjalankan seeder..."
    php artisan db:seed --force || true
else
    echo "WARNING: Database belum siap setelah $max_tries percobaan. Melanjutkan startup Apache..."
fi

# Clear cache agar aplikasi berjalan bersih
php artisan optimize:clear || true

# Pastikan kepemilikan file oleh user web server www-data
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads || true

echo "Memulai Apache..."
exec "$@"
