#!/bin/bash

# CBE Platform Automated Deployment Script
# Usage: bash deploy.sh <target-ip> <target-user> <target-path>

set -e

TARGET_IP=${1:-"192.168.0.101"}
TARGET_USER=${2:-"ubuntu"}
TARGET_PATH=${3:-"/home/ubuntu/cbe"}

echo "================================================"
echo "CBE Platform Deployment Script"
echo "================================================"
echo "Target IP: $TARGET_IP"
echo "Target User: $TARGET_USER"
echo "Target Path: $TARGET_PATH"
echo "================================================"

# Verify SSH connection
echo "[1/8] Verifying SSH connection..."
if ! ssh -o ConnectTimeout=5 "$TARGET_USER@$TARGET_IP" "echo 'SSH OK'"; then
    echo "❌ Cannot connect to $TARGET_USER@$TARGET_IP"
    exit 1
fi
echo "✅ SSH connection OK"

# Update system
echo "[2/8] Updating system packages..."
ssh "$TARGET_USER@$TARGET_IP" "sudo apt update && sudo apt upgrade -y" || true

# Install dependencies
echo "[3/8] Installing dependencies..."
ssh "$TARGET_USER@$TARGET_IP" << 'EOF'
sudo apt install -y php8.2 php8.2-{cli,fpm,mysql,xml,curl,mbstring,zip,gd,bcmath,sqlite3,pdo-sqlite}
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs git composer nginx
EOF
echo "✅ Dependencies installed"

# Create application directory
echo "[4/8] Creating application directory..."
ssh "$TARGET_USER@$TARGET_IP" "mkdir -p $TARGET_PATH && cd $TARGET_PATH && pwd"

# Clone repository (or copy if not git)
echo "[5/8] Deploying application code..."
if [ -d ".git" ]; then
    echo "  Using git clone..."
    ssh "$TARGET_USER@$TARGET_IP" "cd $TARGET_PATH && git clone . 2>/dev/null || echo 'Already exists'"
else
    echo "  Using scp copy..."
    ssh "$TARGET_USER@$TARGET_IP" "rm -rf $TARGET_PATH/*"
    scp -r ./* "$TARGET_USER@$TARGET_IP:$TARGET_PATH/" || true
fi

# Install PHP dependencies
echo "[6/8] Installing PHP dependencies..."
ssh "$TARGET_USER@$TARGET_IP" "cd $TARGET_PATH && composer install --no-dev -q"

# Build assets
echo "[7/8] Building front-end assets..."
ssh "$TARGET_USER@$TARGET_IP" "cd $TARGET_PATH && npm install -q && npm run build -q" || true

# Configure environment and permissions
echo "[8/8] Configuring application..."
ssh "$TARGET_USER@$TARGET_IP" << SETUP_EOF
cd $TARGET_PATH

# Create .env
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || echo "APP_KEY=base64:\$(openssl rand -base64 32)" > .env.basic
fi

# Generate app key
php artisan key:generate --force

# Create database
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate --force

# Set permissions
sudo chown -R www-data:www-data $TARGET_PATH
sudo chmod -R 755 $TARGET_PATH
sudo chmod -R 775 $TARGET_PATH/storage
sudo chmod -R 775 $TARGET_PATH/bootstrap/cache

# Configure Nginx
sudo tee /etc/nginx/sites-available/cbe > /dev/null << 'NGINX_CONF'
server {
    listen 8001;
    server_name _;
    root $TARGET_PATH/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.html index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_CONF

# Enable Nginx site
sudo ln -sf /etc/nginx/sites-available/cbe /etc/nginx/sites-enabled/cbe
sudo rm -f /etc/nginx/sites-enabled/default

# Start services
sudo systemctl start php8.2-fpm nginx
sudo systemctl enable php8.2-fpm nginx

# Verify
echo "Waiting for services to start..."
sleep 2
curl -s http://localhost:8001 > /dev/null && echo "✅ Application is running on http://localhost:8001"

SETUP_EOF

echo ""
echo "================================================"
echo "✅ Deployment Complete!"
echo "================================================"
echo ""
echo "Access your CBE Platform:"
echo "  Students: http://$TARGET_IP:8001/learn/login"
echo "  Teachers: http://$TARGET_IP:8001/teacher/login"
echo "  Admin:    http://$TARGET_IP:8001/admin/login"
echo "  Map:      http://$TARGET_IP:8001/devices"
echo ""
echo "Default credentials:"
echo "  Admin:    admin1 / admin123"
echo "  Teacher:  teacher1 / teacher123"
echo "  Student:  student1 / student123"
echo ""
echo "Troubleshooting:"
echo "  SSH into target: ssh $TARGET_USER@$TARGET_IP"
echo "  Check logs: tail -50 $TARGET_PATH/storage/logs/laravel.log"
echo "  Database: sqlite3 $TARGET_PATH/database/database.sqlite"
echo ""
