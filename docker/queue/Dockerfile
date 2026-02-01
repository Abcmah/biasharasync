FROM php:8.2-cli

# Add docker-php-extension-installer script
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install php extensions
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    pdo_pgsql


# install application dependencies
WORKDIR /var/www/app

COPY ./composer.json ./composer.lock* ./

RUN composer install --no-scripts --no-autoloader --ansi --no-interaction

# copy entrypoint files
COPY ./docker/app/docker-php-* /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-php-entrypoint
RUN dos2unix /usr/local/bin/docker-php-entrypoint-dev

RUN chmod +x /usr/local/bin/docker-php-entrypoint \
   && chmod +x /usr/local/bin/docker-php-entrypoint-dev


WORKDIR /var/www/app

COPY --chown=www-data:www-data  . .

RUN composer dump-autoload -o \
    && chmod -R 775 /var/www/app/storage /var/www/app/bootstrap/cache

COPY ./docker/queue/supervisor.conf /etc/supervisord.conf

# run supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
