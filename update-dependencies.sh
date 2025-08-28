#!/bin/bash

# Dependency Update Script for Kidzklinika v2
# This script safely updates all dependencies for production

echo "🔄 Starting dependency update process..."

# Backup current state
echo "📦 Creating backup of current dependencies..."
cp composer.json composer.json.backup
cp package.json package.json.backup

# Update Composer dependencies
echo "📦 Updating Composer dependencies..."
composer update --no-dev --optimize-autoloader

# Check for any Composer errors
if [ $? -ne 0 ]; then
    echo "❌ Composer update failed. Restoring backup..."
    cp composer.json.backup composer.json
    composer install --no-dev --optimize-autoloader
    exit 1
fi

# Update NPM dependencies
echo "📦 Updating NPM dependencies..."
npm update

# Check for any NPM errors
if [ $? -ne 0 ]; then
    echo "❌ NPM update failed. Restoring backup..."
    cp package.json.backup package.json
    npm install
    exit 1
fi

# Build assets
echo "🔨 Building production assets..."
npm run build

# Check for build errors
if [ $? -ne 0 ]; then
    echo "❌ Asset build failed."
    exit 1
fi

# Clear and cache configurations
echo "🗂️  Clearing and caching configurations..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check for Laravel errors
if [ $? -ne 0 ]; then
    echo "❌ Laravel cache operations failed."
    exit 1
fi

# Clean up backups
echo "🧹 Cleaning up backup files..."
rm composer.json.backup package.json.backup

echo "✅ Dependency update completed successfully!"
echo "📋 Next steps:"
echo "   1. Test the application thoroughly"
echo "   2. Check for any breaking changes"
echo "   3. Review the changelog of updated packages"
echo "   4. Deploy to staging environment first"
