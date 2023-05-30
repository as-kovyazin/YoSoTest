FROM php:7.4.2-fpm

ARG userGroup
ARG userName

RUN addgroup --system --gid 1000 ${userGroup}
RUN adduser --system --uid 1000 --ingroup ${userGroup} ${userName}

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt update && apt install -y git wget curl zip unzip zlib1g-dev libzip-dev libpq-dev libicu-dev
RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig
RUN curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig
RUN php composer-setup.php
RUN rm composer-setup.php*
RUN chmod +x composer.phar
RUN mv composer.phar /usr/bin/composer
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install pgsql
RUN pecl install -o -f redis
RUN docker-php-ext-enable redis
RUN pecl install xdebug-3.1.5
RUN docker-php-ext-enable xdebug
RUN rm -rf /tmp/pear
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY start.sh /usr/bin/start.sh
RUN chmod +x /usr/bin/start.sh

USER ${userName}

WORKDIR /var/www/html

ENTRYPOINT ["/usr/bin/start.sh"]
