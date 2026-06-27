# CBE Platform Deployment Guide

## System Requirements

### Hardware
- **CPU**: 2+ cores
- **RAM**: 4GB minimum (8GB recommended)
- **Storage**: 50GB+ SSD
- **Network**: 100Mbps+ connection

### Software
- **OS**: Ubuntu 20.04 LTS or later / Debian 11+
- **PHP**: 8.2 or higher
- **MySQL/MariaDB**: 5.7 or later
- **Node.js**: 18+ (for asset compilation)
- **Composer**: Latest version
- **Git**: Latest version

---

## Pre-Deployment Checklist

- [ ] Target machine has Ubuntu/Debian installed
- [ ] SSH access to target machine configured
- [ ] Sufficient disk space available
- [ ] Internet connectivity verified
- [ ] You have the IP address or hostname of target machine

---

## Deployment Steps

### 1. SSH into Target Machine

```bash
ssh user@TARGET_IP
# or
ssh user@TARGET_HOSTNAME
```

### 2. Update System

```bash
sudo apt update && sudo apt upgrade -y
```

### 3. Install Dependencies

```bash
# PHP and Extensions
sudo apt install -y php8.2 php8.2-{cli,fpm,mysql,xml,curl,mbstring,zip,gd,bcmath,sqlite3,pdo-sqlite}

# Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# MySQL (optional, for production)
sudo apt install -y mysql-server

# Git and Composer
sudo apt install -y git composer
```

### 4. Clone Repository

```bash
cd /home/user  # or your desired directory
git clone https://github.com/kenyaone/cbe.git
cd cbe
```

### 5. Install Laravel Dependencies

```bash
composer install --no-dev
npm install
npm run build
```

### 6. Set Up Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME="CBE Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://TARGET_IP:8001

DB_CONNECTION=sqlite
DB_DATABASE=/home/user/cbe/database/database.sqlite

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync
```

### 7. Import Database (if migrating from existing)

**On source machine (current machine):**
```bash
sqlite3 /home/tele/cbe-platform/database/database.sqlite ".dump" > cbe_database.sql
scp cbe_database.sql user@TARGET_IP:/tmp/
```

**On target machine:**
```bash
sqlite3 /home/user/cbe/database/database.sqlite < /tmp/cbe_database.sql
```

OR skip this and start fresh (seeders will populate data):
```bash
php artisan migrate
php artisan db:seed
```

### 8. Set File Permissions

```bash
sudo chown -R www-data:www-data /home/user/cbe
sudo chmod -R 755 /home/user/cbe
sudo chmod -R 775 /home/user/cbe/storage
sudo chmod -R 775 /home/user/cbe/bootstrap/cache
```

### 9. Configure Web Server (Nginx)

```bash
sudo apt install -y nginx

sudo nano /etc/nginx/sites-available/cbe
```

Paste this config:
```nginx
server {
    listen 8001;
    server_name _;
    root /home/user/cbe/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.html index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/cbe /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

### 10. Start PHP-FPM

```bash
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
```

### 11. Verify Installation

```bash
# Check if running
curl http://localhost:8001

# Check logs
tail -50 /home/user/cbe/storage/logs/laravel.log
```

---

## Post-Deployment Verification

```bash
# Test student login endpoint
curl http://TARGET_IP:8001/learn/login

# Test public device map
curl http://TARGET_IP:8001/devices

# Test API
curl http://TARGET_IP:8001/devices/api

# Check database
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::table('users')->count();
```

---

## Accessing the Platform

Once running:

| Role | URL |
|------|-----|
| **Students** | http://TARGET_IP:8001/learn/login |
| **Teachers** | http://TARGET_IP:8001/teacher/login |
| **Admin** | http://TARGET_IP:8001/admin/login |
| **Public Map** | http://TARGET_IP:8001/devices |
| **Device API** | http://TARGET_IP:8001/devices/api |

---

## Troubleshooting

### Port Already in Use
```bash
sudo lsof -i :8001
sudo kill -9 PID
```

### Permission Denied
```bash
sudo chown -R www-data:www-data /home/user/cbe
```

### Database Not Found
```bash
touch /home/user/cbe/database/database.sqlite
chmod 664 /home/user/cbe/database/database.sqlite
```

### PHP-FPM Not Running
```bash
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
```

---

## Next Steps

1. Provide the target machine IP/hostname
2. SSH into the machine and run the deployment steps
3. Test the URLs listed above
4. Share the public device map with stakeholders

Need help with any specific step?
