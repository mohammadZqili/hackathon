#!/bin/sh

set -e

echo "Starting GuacPanel Hackathon Management System..."

# Wait for database to be ready (if using external database)
echo "Checking application setup..."

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear all caches
echo "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations and seeders
echo "Running database migrations..."
php artisan migrate --force

# Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link

# Cache configurations for better performance
echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

echo "Application setup completed successfully!"
echo "Starting services..."

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
