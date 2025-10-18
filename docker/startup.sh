#!/bin/sh

# NextCode Group - Docker Startup Script

echo "🚀 Starting NextCode Group services..."

# Start PHP-FPM
echo "📦 Starting PHP-FPM..."
php-fpm -D

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
while ! mysqladmin ping -h mysql -u nextcode_user -psecure_password_123 --silent; do
    echo "Waiting for MySQL..."
    sleep 2
done
echo "✅ MySQL is ready!"

# Run database migrations if needed
echo "🗄️ Checking database setup..."
if [ ! -f /var/www/html/.database_setup ]; then
    echo "Setting up database..."
    php /var/www/html/setup.php
    touch /var/www/html/.database_setup
    echo "✅ Database setup completed!"
fi

# Clear and warm up caches
echo "🧹 Clearing caches..."
php /var/www/html/clear-cache.php

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/logs

# Start Nginx
echo "🌐 Starting Nginx..."
nginx -g "daemon off;" &

# Health check
echo "🏥 Starting health check..."
while true; do
    if curl -f http://localhost/health > /dev/null 2>&1; then
        echo "✅ Application is healthy"
    else
        echo "❌ Application health check failed"
    fi
    sleep 30
done
