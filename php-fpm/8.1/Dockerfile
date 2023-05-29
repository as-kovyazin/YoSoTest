FROM php:8.1.12-fpm

ARG userGroup
ARG userName

RUN addgroup --system --gid 1000 ${userGroup} && adduser --system --uid 1000 --ingroup ${userGroup} ${userName}

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt update && apt install -y git wget curl zip unzip zlib1g-dev libzip-dev libpq-dev libicu-dev
RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer \
        && wget https://get.symfony.com/cli/installer -O - | bash \
        && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN docker-php-ext-install pdo zip \
    && pecl install -o -f redis \
    && pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis
RUN docker-php-ext-install pdo_pgsql pgsql
RUN docker-php-ext-configure intl && docker-php-ext-install intl

COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER ${userName}

WORKDIR /var/www/html
CMD ["php-fpm"]
