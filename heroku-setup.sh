#!/bin/bash

# Create writable directories in /tmp for Heroku
mkdir -p /tmp/storage/framework/{sessions,views,cache}
mkdir -p /tmp/storage/logs
mkdir -p /tmp/bootstrap/cache
mkdir -p /tmp/uploads/{business_logos,documents,img,invoice_logos}

# Clear any existing compiled views to prevent cache issues (after mkdir)
rm -rf /tmp/storage/framework/views/*
rm -rf /tmp/bootstrap/cache/*

# Set permissions
chmod -R 777 /tmp/storage /tmp/bootstrap/cache /tmp/uploads

# Create symlinks for storage and bootstrap directories
if [ ! -L storage ]; then
    rm -rf storage
    ln -s /tmp/storage storage
fi

if [ ! -L bootstrap/cache ]; then
    rm -rf bootstrap/cache
    mkdir -p bootstrap
    ln -s /tmp/bootstrap/cache bootstrap/cache
fi

# Create symlink for uploads
if [ ! -L public/uploads ]; then
    rm -rf public/uploads
    ln -s /tmp/uploads public/uploads
fi

echo "Heroku setup complete"
