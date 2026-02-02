FROM php:8.3-apache

# Arguments
ARG container_project_path=/var/www/html/
ARG uid=1000
ARG user=bam

# Install system dependencies & PHP extensions
RUN curl -sSLf -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && apt-get update && apt-get install -y --no-install-recommends \
        unzip git \
    && rm -rf /var/lib/apt/lists/* \
    && IPE_GD_WITHOUTAVIF=1 install-php-extensions \
        bcmath exif gd intl pcntl pdo_mysql imagick redis zip

# Install Composer
COPY --from=composer:2.7.7 /usr/bin/composer /usr/local/bin/composer

# Create system user to match host UID
RUN useradd -G www-data -u $uid -d /home/$user -m $user

# Set work directory
WORKDIR $container_project_path

# Apache configuration
COPY ./docker/.configs/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite && a2enmod deflate

# Copy Entrypoint
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# We stay as ROOT for the build to set permissions,
# then the entrypoint handles the switch or Apache handles the child processes.
ENTRYPOINT ["entrypoint.sh"]
