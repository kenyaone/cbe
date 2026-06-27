# CBE Platform — Quick Start Deployment

## 🚀 TL;DR Deployment (5 minutes)

You have **two paths**:

### Path A: Automated Deployment (Recommended)

```bash
# 1. Provide target details
TARGET_IP="192.168.0.101"
TARGET_USER="ubuntu"
TARGET_PATH="/home/ubuntu/cbe"

# 2. Run automated deployment
bash deploy.sh $TARGET_IP $TARGET_USER $TARGET_PATH

# 3. Done! Access at http://192.168.0.101:8001
```

### Path B: Automated with Database Migration

```bash
# 1. First deploy the application
bash deploy.sh $TARGET_IP $TARGET_USER $TARGET_PATH

# 2. Then migrate your current database
bash migrate-database.sh $TARGET_IP $TARGET_USER $TARGET_PATH

# 3. Login with existing credentials: student1 / student123
```

---

## 📋 What Gets Deployed

✅ **Full CBE Platform with:**
- 6 grades (CBE + 8-4-4)
- 6 subjects (Math, English, Science, etc.)
- 50+ lessons with videos, PDFs, interactive content
- Complete offline-first architecture
- Cloud sync infrastructure
- Public device map
- Admin + Teacher + Student dashboards

✅ **Authentication:**
- Admin: `admin1` / `admin123`
- Teacher: `teacher1` / `teacher123`
- Student: `student1` / `student123`

✅ **Access Points:**
- Student Portal: `http://TARGET_IP:8001/learn`
- Teacher Dashboard: `http://TARGET_IP:8001/teacher`
- Admin Dashboard: `http://TARGET_IP:8001/admin`
- Public Device Map: `http://TARGET_IP:8001/devices`
- Device API: `http://TARGET_IP:8001/devices/api`

---

## 🖥️ System Requirements

**Target Machine Must Have:**
- Ubuntu 20.04+ or Debian 11+
- SSH access
- 4GB RAM minimum
- 50GB disk space
- Internet connection

**On Source Machine (current server):**
- SSH key pair configured
- `sqlite3` installed
- Bash shell

---

## 🔧 Before You Start

### 1. Get Target Machine IP

```bash
# If deploying to local LAN:
ping TARGET_IP

# If deploying to cloud server:
ssh-keygen -t rsa -b 4096  # Generate SSH key if needed
ssh-copy-id ubuntu@TARGET_IP  # Copy SSH key for passwordless access
```

### 2. Verify SSH Works

```bash
ssh ubuntu@TARGET_IP "uname -a"
# Should return Linux system info
```

### 3. (Optional) Backup Current Database

```bash
cp /home/tele/cbe-platform/database/database.sqlite \
   /home/tele/cbe-platform/database/database.sqlite.backup
```

---

## 📱 Deployment Steps

### Step 1: SSH to Target Machine (Manual)

```bash
ssh ubuntu@TARGET_IP

# Then follow the guide below
```

### Step 2: Quick Install (On Target Machine)

```bash
# Copy this entire block and paste into SSH terminal:

cd /home/ubuntu
mkdir -p cbe
cd cbe

# Update system
sudo apt update && sudo apt upgrade -y

# Install dependencies (one command)
sudo apt install -y \
  php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml \
  php8.2-curl php8.2-mbstring php8.2-zip php8.2-gd \
  php8.2-bcmath php8.2-sqlite3 php8.2-pdo-sqlite \
  nginx git composer nodejs npm

# Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### Step 3: Deploy Code

**Option A: From source machine (if no git repo)**
```bash
# On SOURCE machine
cd /home/tele/cbe-platform
scp -r ./* ubuntu@TARGET_IP:/home/ubuntu/cbe/
```

**Option B: Using git (if repo exists)**
```bash
# On TARGET machine
cd /home/ubuntu/cbe
git clone https://github.com/your-repo/cbe.git .
```

### Step 4: Install Dependencies

```bash
cd /home/ubuntu/cbe

# PHP dependencies
composer install --no-dev

# Node dependencies
npm install
npm run build
```

### Step 5: Configure Environment

```bash
# Generate environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Create database
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate

# (Optional) Seed demo data if first time
php artisan db:seed
```

### Step 6: Set Permissions

```bash
sudo chown -R www-data:www-data /home/ubuntu/cbe
sudo chmod -R 755 /home/ubuntu/cbe
sudo chmod -R 775 /home/ubuntu/cbe/storage
sudo chmod -R 775 /home/ubuntu/cbe/bootstrap/cache
```

### Step 7: Configure Web Server

```bash
# Create Nginx config
sudo tee /etc/nginx/sites-available/cbe > /dev/null << 'EOF'
server {
    listen 8001;
    server_name _;
    root /home/ubuntu/cbe/public;

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

    location ~ /\.(?!well-known).* { deny all; }
}
EOF

# Enable Nginx
sudo ln -sf /etc/nginx/sites-available/cbe /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Restart services
sudo systemctl restart php8.2-fpm nginx
sudo systemctl enable php8.2-fpm nginx
```

### Step 8: Verify Installation

```bash
# Check if running
curl http://localhost:8001

# Should see CBE Platform page (or redirect to login)
```

---

## 📊 Access Your Deployment

Once running, open browser and navigate to:

| What | URL |
|------|-----|
| **Homepage** | `http://192.168.0.101:8001` |
| **Student Login** | `http://192.168.0.101:8001/learn/login` |
| **Teacher Dashboard** | `http://192.168.0.101:8001/teacher/login` |
| **Admin Dashboard** | `http://192.168.0.101:8001/admin/login` |
| **Public Device Map** | `http://192.168.0.101:8001/devices` |
| **API Endpoint** | `http://192.168.0.101:8001/devices/api` |

**Test Credentials:**
```
Student: student1 / student123
Teacher: teacher1 / teacher123
Admin:   admin1 / admin123
```

---

## 📦 Database Migration

### Option 1: Migrate Existing Data

**On source machine (192.168.0.100):**
```bash
cd /home/tele/cbe-platform

# Export current database
sqlite3 database/database.sqlite ".dump" > /tmp/cbe_database.sql

# Copy to target
scp /tmp/cbe_database.sql ubuntu@TARGET_IP:/tmp/
```

**On target machine:**
```bash
# Import the database
sqlite3 /home/ubuntu/cbe/database/database.sqlite < /tmp/cbe_database.sql

# Verify
sqlite3 /home/ubuntu/cbe/database/database.sqlite "SELECT COUNT(*) FROM users;"
# Should show at least 3 (admin, teacher, student)
```

### Option 2: Fresh Start

**On target machine:**
```bash
cd /home/ubuntu/cbe

# Seed demo data
php artisan db:seed

# Verify
php artisan tinker
>>> DB::table('users')->count()
>>> DB::table('subjects')->count()
```

---

## 🧪 Testing Checklist

After deployment, verify:

### API Health
```bash
curl http://TARGET_IP:8001/devices/api
# Should return JSON with device data
```

### Database
```bash
sqlite3 /home/ubuntu/cbe/database/database.sqlite ".tables"
# Should show: users, grades, subjects, lessons, learner_progress, etc.
```

### Authentication
```bash
# Try login as student
curl -X POST http://TARGET_IP:8001/learn/login \
  -d "username=student1&password=student123" \
  -c cookies.txt

# Check device map
curl http://TARGET_IP:8001/devices | grep -o "device"
```

### Logs
```bash
tail -50 /home/ubuntu/cbe/storage/logs/laravel.log
# Should show no errors (warnings OK)
```

---

## 🆘 Troubleshooting

### "Connection refused"
```bash
# Check if Nginx is running
sudo systemctl status nginx

# Restart it
sudo systemctl restart nginx php8.2-fpm
```

### "Database not found"
```bash
# Check if database exists
ls -la /home/ubuntu/cbe/database/database.sqlite

# Create if missing
touch /home/ubuntu/cbe/database/database.sqlite
chmod 664 /home/ubuntu/cbe/database/database.sqlite
```

### "Permission denied"
```bash
# Fix permissions
sudo chown -R www-data:www-data /home/ubuntu/cbe
sudo chmod -R 775 /home/ubuntu/cbe/storage
```

### "composer not found"
```bash
# Install composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### "Cannot connect to 8001"
```bash
# Check if port is in use
sudo lsof -i :8001

# If something else is using it, kill it or use different port
# Edit /etc/nginx/sites-available/cbe and change "listen 8001" to another port
```

---

## 🎯 Next Steps After Deployment

1. **✅ Verify** the system is running and accessible
2. **📱 Test** with student/teacher/admin accounts
3. **🗺️ Share** public device map: `http://TARGET_IP:8001/devices`
4. **🔄 Set up** cloud sync if deploying remote devices
5. **📊 Monitor** device checkins via admin dashboard
6. **🔐 Configure** backups for database
7. **🌐 (Optional)** Set up domain/SSL for production

---

## 📞 Support

**Quick Diagnostic:**
```bash
cd /home/ubuntu/cbe
php artisan tinker
>>> config('app.url')
>>> DB::connection()->getDatabaseName()
>>> DB::table('users')->first()
```

**Check Error Logs:**
```bash
tail -100 /home/ubuntu/cbe/storage/logs/laravel.log
```

**Reset Everything (CAREFUL!):**
```bash
php artisan migrate:reset
php artisan migrate
php artisan db:seed
```

---

## 📁 Files Provided

| File | Purpose |
|------|---------|
| `DEPLOYMENT_GUIDE.md` | Detailed step-by-step guide |
| `DEPLOYMENT_CHECKLIST.md` | Full checklist with verification |
| `deploy.sh` | Automated deployment script |
| `migrate-database.sh` | Database migration script |
| `.env.example` | Environment configuration template |
| `QUICKSTART_DEPLOYMENT.md` | This file |

---

**Ready? Provide the target IP and run:**
```bash
bash deploy.sh YOUR_TARGET_IP ubuntu /home/ubuntu/cbe
```

**Need help?** Refer to `DEPLOYMENT_GUIDE.md` or `DEPLOYMENT_CHECKLIST.md`

