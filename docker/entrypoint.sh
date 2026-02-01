#!/bin/bash
set -e

# Ensure we are in the right place
cd /var/www/html

# Install dependencies if vendor is missing or needs update
if [ ! -d "vendor" ]; then
    composer install --no-interaction --optimize-autoloader
fi

# Ensure Laravel storage/cache permissions are correct
# These often get messed up when mounting volumes from different OS/Users
mkdir -p storage/framework/{sessions,views,cache}
chmod -R 775 storage bootstrap/cache
chown -R $user:www-data storage bootstrap/cache

# Run the original Apache command
exec apache2-foreground
