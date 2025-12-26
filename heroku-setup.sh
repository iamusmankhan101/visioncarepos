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

# Remove or rename install directory to prevent installation check
# Commented out to allow installation on first deploy
# if [ -d "public/install" ]; then
#     mv public/install public/install_disabled
# fi

# Create symlink for uploads
if [ ! -L public/uploads ]; then
    rm -rf public/uploads
    ln -s /tmp/uploads public/uploads
fi

echo "Heroku setup complete"
