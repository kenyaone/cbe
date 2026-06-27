# CBE Platform Deployment Checklist

## Pre-Deployment

### Source Machine (Current Server - 192.168.0.100)
- [ ] Backup current database
- [ ] Document current credentials (admin, teacher, student)
- [ ] Note any customizations made to .env
- [ ] Take screenshot of current dashboard for reference

### Target Machine
- [ ] Ubuntu 20.04 LTS or Debian 11+ installed
- [ ] SSH access configured and working
- [ ] Sufficient disk space (50GB+ recommended)
- [ ] Internet connectivity verified
- [ ] IP address/hostname noted: _______________

---

## Quick Deployment (Automated)

If you have SSH access to target machine:

```bash
# From source machine directory
cd /home/tele/cbe-platform

# Make scripts executable
chmod +x deploy.sh migrate-database.sh

# Deploy application (replace with your details)
bash deploy.sh 192.168.0.101 ubuntu /home/ubuntu/cbe

# OR migrate existing database
bash migrate-database.sh 192.168.0.101 ubuntu /home/ubuntu/cbe
```

---

## Manual Deployment (Step-by-Step)

### Phase 1: SSH and System Setup

```bash
ssh ubuntu@TARGET_IP

# Update system
sudo apt update && sudo apt upgrade -y

# Install dependencies
sudo apt install -y php8.2 php8.2-{cli,fpm,mysql,xml,curl,mbstring,zip,gd,bcmath,sqlite3,pdo-sqlite}
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs git composer nginx
```

- [ ] System updated
- [ ] All dependencies installed
- [ ] PHP version check: `php -v`
- [ ] Node version check: `node -v`

### Phase 2: Application Setup

```bash
# Create directory
mkdir -p /home/ubuntu/cbe
cd /home/ubuntu/cbe

# Copy application code (scp from source machine)
# From source: scp -r /home/tele/cbe-platform/* ubuntu@192.168.0.101:/home/ubuntu/cbe/

# Install PHP packages
composer install --no-dev

# Install and build frontend
npm install
npm run build

# Generate app key
php artisan key:generate

# Create database
touch database/database.sqlite
chmod 664 database/database.sqlite
```

- [ ] Code copied
- [ ] Composer dependencies installed
- [ ] NPM dependencies installed
- [ ] App key generated
- [ ] Database file created

### Phase 3: Database Migration

**Option A: Import existing data**
```bash
# From source machine
sqlite3 /home/tele/cbe-platform/database/database.sqlite ".dump" > /tmp/cbe_database.sql
scp /tmp/cbe_database.sql ubuntu@TARGET_IP:/tmp/

# On target machine
sqlite3 /home/ubuntu/cbe/database/database.sqlite < /tmp/cbe_database.sql
```

**Option B: Fresh install**
```bash
# On target machine
php artisan migrate
php artisan db:seed
```

- [ ] Database ready with data

### Phase 4: Permissions & Web Server

```bash
# Set permissions
sudo chown -R www-data:www-data /home/ubuntu/cbe
sudo chmod -R 755 /home/ubuntu/cbe
sudo chmod -R 775 /home/ubuntu/cbe/storage
sudo chmod -R 775 /home/ubuntu/cbe/bootstrap/cache

# Configure Nginx
sudo nano /etc/nginx/sites-available/cbe
# (Paste config from DEPLOYMENT_GUIDE.md)

# Enable site
sudo ln -sf /etc/nginx/sites-available/cbe /etc/nginx/sites-enabled/
sudo systemctl restart nginx

# Start PHP-FPM
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
```

- [ ] File permissions set
- [ ] Nginx configured and running
- [ ] PHP-FPM running

---

## Post-Deployment Verification

### Quick Tests

```bash
# Test from target machine
curl http://localhost:8001

# Test from any machine
curl http://TARGET_IP:8001

# Access points
curl http://TARGET_IP:8001/learn/login     # Student login
curl http://TARGET_IP:8001/teacher/login   # Teacher login
curl http://TARGET_IP:8001/admin/login     # Admin login
curl http://TARGET_IP:8001/devices         # Public map
```

- [ ] Homepage loads
- [ ] Student login page loads
- [ ] Teacher login page loads
- [ ] Admin login page loads
- [ ] Public device map loads

### Functional Tests

**Database verification:**
```bash
php artisan tinker
>>> DB::table('users')->count()
>>> DB::table('grades')->count()
>>> DB::table('subjects')->count()
```

- [ ] Users table has data
- [ ] Grades configured (6 grades for CBE)
- [ ] Subjects populated (6 subjects)

**Authentication tests:**
- [ ] Student login: `student1` / `student123`
- [ ] Teacher login: `teacher1` / `teacher123`
- [ ] Admin login: `admin1` / `admin123`
- [ ] Browse lessons as student
- [ ] View learner progress as teacher
- [ ] Access cloud dashboard as admin

**API tests:**
```bash
curl http://TARGET_IP:8001/devices/api | python3 -m json.tool

# Should return device data, students, lessons, etc.
```

- [ ] Device API returns JSON
- [ ] Device map displays markers
- [ ] Statistics are accurate

---

## Troubleshooting During Deployment

### Cannot connect via SSH
```bash
# Check if target is reachable
ping TARGET_IP

# Check SSH port
ssh -v ubuntu@TARGET_IP

# Or provide IP/port if not standard
ssh -p 2222 ubuntu@TARGET_IP
```

### Composer or npm errors
```bash
# Clear cache and retry
composer clear-cache
composer install --no-dev --no-interaction

npm cache clean --force
npm install
```

### Permission denied errors
```bash
# Ensure www-data owns the directory
sudo chown -R www-data:www-data /home/ubuntu/cbe

# And has write permissions on storage
sudo chmod -R 775 /home/ubuntu/cbe/storage
sudo chmod -R 775 /home/ubuntu/cbe/bootstrap/cache
```

### Nginx not starting
```bash
# Check Nginx config
sudo nginx -t

# Check if port 8001 is in use
sudo lsof -i :8001

# Check error logs
sudo tail -20 /var/log/nginx/error.log
```

### PHP-FPM not running
```bash
# Check status
sudo systemctl status php8.2-fpm

# Restart
sudo systemctl restart php8.2-fpm

# Check error logs
sudo tail -20 /var/log/php8.2-fpm.log
```

### Database errors
```bash
# Check if database file exists and is readable
ls -la /home/ubuntu/cbe/database/database.sqlite

# Test SQLite directly
sqlite3 /home/ubuntu/cbe/database/database.sqlite ".tables"

# Rebuild database
php artisan migrate:refresh --seed  # WARNING: Clears all data
```

### Application errors
```bash
# Check Laravel logs
tail -50 /home/ubuntu/cbe/storage/logs/laravel.log

# Enable debug mode in .env temporarily
APP_DEBUG=true

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Post-Deployment Configuration

### 1. Update .env for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://TARGET_IP:8001
LOG_LEVEL=warning
```

### 2. Optional: Set Up External Database (MySQL)

If you want to use MySQL instead of SQLite:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cbe_platform
DB_USERNAME=cbe_user
DB_PASSWORD=strong_password
```

Then:
```bash
php artisan migrate
php artisan db:seed
```

### 3. Configure Domain (if using domain instead of IP)

Update Nginx config:
```nginx
server_name yourdomain.com www.yourdomain.com;
```

And update .env:
```env
APP_URL=https://yourdomain.com
```

### 4. Set Up SSL/HTTPS (recommended for production)

```bash
# Using Let's Encrypt (requires domain)
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com

# Update Nginx to redirect HTTP to HTTPS
```

---

## Data Synchronization

### Local Devices Syncing Data

Once the cloud server is live, local device deployments will:
1. Operate offline using local SQLite databases
2. Queue learner progress in `sync_queue` table
3. Attempt sync when internet is available
4. POST to `http://TARGET_IP:8001/api/sync/upload`

**Verify sync is working:**
- [ ] Monitor device checkins at `/cloud` dashboard
- [ ] Check `cloud_learner_progress` table for synced data
- [ ] Public map shows device status

---

## Backup & Maintenance

### Daily Backup Script

```bash
#!/bin/bash
BACKUP_DIR="/home/ubuntu/cbe-backups"
mkdir -p $BACKUP_DIR
sqlite3 /home/ubuntu/cbe/database/database.sqlite ".dump" > \
  $BACKUP_DIR/cbe_$(date +%Y%m%d_%H%M%S).sql
```

Add to crontab:
```bash
crontab -e
# Add: 0 2 * * * /path/to/backup-script.sh
```

- [ ] Backup script created
- [ ] Cron job configured

---

## Completion

Once all checks pass, you're ready to:
- [ ] Share dashboard with stakeholders at `http://TARGET_IP:8001/devices`
- [ ] Deploy to remote device locations
- [ ] Enable cloud syncing for local devices
- [ ] Monitor platform health via admin dashboard

---

## Support

**Quick Diagnostic Command:**
```bash
cd /home/ubuntu/cbe
php artisan tinker
>>> DB::connection()->getDatabaseName()  // Should show database path
>>> DB::table('users')->count()          // Should show users
>>> DB::table('cloud_devices')->count()  // Should show synced devices
```

**Emergency Reset (if needed):**
```bash
# WARNING: This will delete all data!
php artisan migrate:reset
php artisan migrate
php artisan db:seed
```

---

**Deployment Date: ___________**
**Target IP: ___________**
**Deployed By: ___________**
**Notes:** ___________________________________________________________

