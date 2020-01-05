#!/usr/bin/env bash

find /var/www/html/uploader/project -type f -exec chmod 664 {} \;
find /var/www/html/uploader/project -type d -exec chmod 775 {} \;

chown -R 1000:www-data /var/www/html/uploader/project

if [ ! -f /var/www/html/uploader/project/storage/logs/apache-error.log ]; then
    touch /var/www/html/uploader/project/storage/logs/apache-error.log
fi

if [ ! -f /var/www/html/uploader/project/.env ]; then
    cp /var/www/html/uploader/project/.env.example /var/www/html/uploader/project/.env
fi

chown -R uploader:www-data /var/www/html/uploader

cd /var/www/html/uploader/project/
php vendor/bin/phinx migrate

echo "* * * * * php /var/www/html/uploader/project/tools/cron.php >> /var/log/cronjob.log 2>&1" > /etc/crontabs/root
crond

bash -c "cd /var/www/html/uploader/project/ && composer install --prefer-dist --no-scripts --no-autoloader"

exec /usr/sbin/apache2ctl -D FOREGROUND


