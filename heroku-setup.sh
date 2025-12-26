#!/bin/bash

# Create writable directories in /tmp for Heroku
mkdir -p /tmp/storage/framework/{sessions,views,cache}
mkdir -p /tmp/storage/logs
mkdir -p /tmp/bootstrap/cache

# Create symlinks if they don't exist
if [ ! -L storage/framework/sessions ]; then
    rm -rf storage/framework/sessions
    ln -s /tmp/storage/framework/sessions storage/framework/sessions
fi

if [ ! -L storage/framework/views ]; then
    rm -rf storage/framework/views
    ln -s /tmp/storage/framework/views storage/framework/views
fi

if [ ! -L storage/framework/cache ]; then
    rm -rf storage/framework/cache
    ln -s /tmp/storage/framework/cache storage/framework/cache
fi

if [ ! -L storage/logs ]; then
    rm -rf storage/logs
    ln -s /tmp/storage/logs storage/logs
fi

if [ ! -L bootstrap/cache ]; then
    rm -rf bootstrap/cache
    ln -s /tmp/bootstrap/cache bootstrap/cache
fi

# Set permissions
chmod -R 777 /tmp/storage /tmp/bootstrap/cache

echo "Heroku setup complete"
