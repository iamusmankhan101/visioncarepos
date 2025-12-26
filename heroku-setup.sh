#!/bin/bash

# Copy Heroku environment configuration
if [ ! -f .env ]; then
    cp .env.heroku .env
    chmod 666 .env
fi

# Parse DATABASE_URL if available (Heroku PostgreSQL format)
if [ ! -z "$DATABASE_URL" ]; then
    echo "DATABASE_URL=$DATABASE_URL" >> .env
    
    # Extract database connection details from DATABASE_URL
    # Format: postgres://username:password@host:port/database
    if [[ $DATABASE_URL =~ postgres://([^:]+):([^@]+)@([^:]+):([0-9]+)/(.+) ]]; then
        DB_USERNAME="${BASH_REMATCH[1]}"
        DB_PASSWORD="${BASH_REMATCH[2]}"
        DB_HOST="${BASH_REMATCH[3]}"
        DB_PORT="${BASH_REMATCH[4]}"
        DB_DATABASE="${BASH_REMATCH[5]}"
        
        echo "DB_HOST=$DB_HOST" >> .env
        echo "DB_PORT=$DB_PORT" >> .env
        echo "DB_DATABASE=$DB_DATABASE" >> .env
        echo "DB_USERNAME=$DB_USERNAME" >> .env
        echo "DB_PASSWORD=$DB_PASSWORD" >> .env
    fi
fi

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
