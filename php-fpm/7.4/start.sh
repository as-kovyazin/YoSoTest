#!/bin/bash
cd /var/www/html
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php-fpm