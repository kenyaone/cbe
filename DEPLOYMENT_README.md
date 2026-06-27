# CBE Platform Deployment Package

**Status:** ✅ Ready for deployment to another machine  
**Current System:** Running on 192.168.0.100:8001  
**Deployment Date:** 2026-06-27

---

## 📦 What's Included

This deployment package contains everything you need to deploy the CBE Platform to another Linux server:

### 🎯 Core Files
- **`deploy.sh`** — Fully automated deployment script (hands-off)
- **`migrate-database.sh`** — Database migration from current system
- **`.env.example`** — Pre-configured environment template
- **Complete Laravel 13 application** with all code, migrations, seeders

### 📖 Documentation
1. **`QUICKSTART_DEPLOYMENT.md`** ⭐ **START HERE**
   - Quick 5-minute deployment
   - Step-by-step manual instructions
   - Testing checklist
   
2. **`DEPLOYMENT_GUIDE.md`**
   - Detailed system requirements
   - Complete deployment walkthrough
   - Post-deployment verification
   - Troubleshooting reference
   
3. **`DEPLOYMENT_CHECKLIST.md`**
   - Pre-deployment checklist
   - Phase-by-phase verification
   - Functional tests
   - Troubleshooting common issues

---

## 🚀 Quick Start (Choose One)

### Option A: Fully Automated (Recommended)

```bash
# From current machine
cd /home/tele/cbe-platform

# Automated deployment to new server
bash deploy.sh 192.168.0.101 ubuntu /home/ubuntu/cbe

# ✅ Done! System runs at http://192.168.0.101:8001
```

### Option B: Automated + Database Migration

```bash
# Deploy application first
bash deploy.sh 192.168.0.101 ubuntu /home/ubuntu/cbe

# Migrate your existing data
bash migrate-database.sh 192.168.0.101 ubuntu /home/ubuntu/cbe

# ✅ New system has all your data from current system
```

### Option C: Manual Step-by-Step

See `QUICKSTART_DEPLOYMENT.md` section **"📱 Deployment Steps"**

---

## 📋 System Requirements

**Target Machine:**
- Ubuntu 20.04 LTS or Debian 11+
- 2+ CPU cores
- 4GB RAM minimum
- 50GB disk space
- SSH access
- Internet connection (for installation)

**Source Machine (to run deployment):**
- Bash shell
- SSH client
- `sqlite3` (for database migration)
- Network access to target machine

---

## 🎯 What Gets Deployed

### Application Features
✅ Full CBE Platform with offline-first architecture  
✅ 6 Grades (CBE + 8-4-4 system)  
✅ 6 Subjects (Math, English, Science, Social Studies, Kiswahili, IT)  
✅ 50+ Lessons with videos, PDFs, interactive content  
✅ Cloud sync infrastructure for remote devices  
✅ Public device map with real-time status  
✅ Admin, Teacher, and Student dashboards  

### User Accounts
- **Admin:** `admin1` / `admin123`
- **Teacher:** `teacher1` / `teacher123`
- **Student:** `student1` / `student123`

### Access Points
- **Student Portal:** `/learn`
- **Teacher Dashboard:** `/teacher`
- **Admin Dashboard:** `/admin`
- **Public Map:** `/devices`
- **API:** `/devices/api`

---

## 🔧 How The Scripts Work

### `deploy.sh` — Automated Deployment

**What it does:**
1. Verifies SSH connectivity
2. Updates target system packages
3. Installs PHP 8.2, Node.js, Nginx, MySQL, Composer
4. Deploys application code
5. Installs PHP and Node dependencies
6. Generates app key
7. Runs database migrations
8. Configures Nginx web server
9. Sets up PHP-FPM
10. Tests the deployment
11. Outputs access URLs

**Usage:**
```bash
bash deploy.sh <target-ip> <target-user> <target-path>

# Example
bash deploy.sh 192.168.0.101 ubuntu /home/ubuntu/cbe
```

### `migrate-database.sh` — Database Migration

**What it does:**
1. Exports current SQLite database
2. Backs up existing database on target
3. Transfers export to target server
4. Imports data into new database
5. Verifies data integrity

**Usage:**
```bash
bash migrate-database.sh <target-ip> <target-user> <target-path>

# After: bash deploy.sh has already run
bash migrate-database.sh 192.168.0.101 ubuntu /home/ubuntu/cbe
```

---

## 🧪 Verification After Deployment

Once deployment completes, test these:

### Browser Tests
```
✅ http://192.168.0.101:8001              → Homepage
✅ http://192.168.0.101:8001/learn        → Student portal
✅ http://192.168.0.101:8001/admin        → Admin dashboard
✅ http://192.168.0.101:8001/devices      → Public device map
```

### API Test
```bash
curl http://192.168.0.101:8001/devices/api | python3 -m json.tool
# Should return JSON with device data
```

### Database Test
```bash
ssh ubuntu@192.168.0.101
cd /home/ubuntu/cbe
sqlite3 database/database.sqlite "SELECT COUNT(*) FROM users;"
# Should return 3+ (admin, teacher, student)
```

---

## 📁 File Structure

```
cbe-platform/
├── DEPLOYMENT_README.md          ← Overview (this file)
├── QUICKSTART_DEPLOYMENT.md      ← 5-minute quick start ⭐
├── DEPLOYMENT_GUIDE.md           ← Detailed guide
├── DEPLOYMENT_CHECKLIST.md       ← Full verification checklist
├── deploy.sh                     ← Automated deployment script
├── migrate-database.sh           ← Database migration script
├── .env.example                  ← Environment configuration
│
├── app/                          ← Laravel application
├── bootstrap/                    ← Bootstrap files
├── config/                       ← Configuration
├── database/
│   ├── database.sqlite           ← Current SQLite database
│   ├── migrations/               ← Database migrations
│   └── seeders/                  ← Database seeders
├── public/                       ← Web root
├── resources/
│   └── views/                    ← Blade templates
├── routes/                       ← Route definitions
├── storage/                      ← Logs, cache, sessions
└── ...
```

---

## 🌐 Deployment Scenarios

### Scenario 1: Deploy to Local LAN
```bash
# Target machine on local network
bash deploy.sh 192.168.0.101 ubuntu /home/ubuntu/cbe

# Access from any device on network
# http://192.168.0.101:8001
```

### Scenario 2: Deploy to Cloud Server
```bash
# Setup SSH key first
ssh-keygen -t rsa
ssh-copy-id ubuntu@cloud-server.example.com

# Deploy
bash deploy.sh cloud-server.example.com ubuntu /var/www/cbe

# Access from anywhere
# http://cloud-server.example.com:8001
```

### Scenario 3: Migrate to Existing Server
```bash
# First, deploy fresh application
bash deploy.sh 192.168.0.101 ubuntu /home/ubuntu/cbe

# Then migrate your database with all history
bash migrate-database.sh 192.168.0.101 ubuntu /home/ubuntu/cbe

# New system has all your data!
```

---

## 🔒 Security Considerations

### Before Production Deployment

- [ ] Change default passwords (admin1, teacher1, student1)
- [ ] Update `.env` with `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure strong database password if using MySQL
- [ ] Set up HTTPS/SSL certificate
- [ ] Configure firewall rules
- [ ] Set up automated backups

### Production Checklist

```bash
# Update .env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Generate new app key
php artisan key:generate --force

# Clear cached config
php artisan config:clear
php artisan cache:clear

# Create backup
sqlite3 database/database.sqlite ".dump" > backup_$(date +%Y%m%d).sql
```

---

## 🆘 Troubleshooting

### Common Issues

**Can't connect via SSH:**
- Verify target machine is running
- Check IP address: `ping TARGET_IP`
- Verify SSH service: `sudo systemctl status ssh`
- Check SSH key: `ssh-copy-id ubuntu@TARGET_IP`

**Port 8001 already in use:**
- Find what's using it: `sudo lsof -i :8001`
- Edit Nginx config to use different port
- Or kill the process: `sudo kill -9 PID`

**Database errors after migration:**
- Verify database imported: `sqlite3 database.sqlite ".tables"`
- Check file permissions: `chmod 664 database.sqlite`
- Test connection: `php artisan tinker` → `DB::connection()->getPdo()`

**Permission denied errors:**
- Fix ownership: `sudo chown -R www-data:www-data /home/ubuntu/cbe`
- Fix permissions: `sudo chmod -R 775 /home/ubuntu/cbe/storage`

**Application won't start:**
- Check logs: `tail -50 storage/logs/laravel.log`
- Verify PHP-FPM: `sudo systemctl status php8.2-fpm`
- Clear cache: `php artisan cache:clear`

See **`DEPLOYMENT_GUIDE.md`** and **`DEPLOYMENT_CHECKLIST.md`** for detailed troubleshooting.

---

## 📞 Support Resources

| Need | Check |
|------|-------|
| Quick start | `QUICKSTART_DEPLOYMENT.md` |
| Step-by-step | `DEPLOYMENT_GUIDE.md` |
| Verification | `DEPLOYMENT_CHECKLIST.md` |
| Automated setup | `deploy.sh` |
| Database transfer | `migrate-database.sh` |
| Configuration | `.env.example` |

---

## 🎯 After Successful Deployment

1. **Verify system is running** at target URL
2. **Test with default credentials** (admin1/teacher1/student1)
3. **Share public map** with stakeholders: `/devices`
4. **Set up local device syncing** if deploying to remote locations
5. **Configure automated backups**
6. **Monitor cloud dashboard** for incoming device data
7. **(Optional) Set up SSL/HTTPS** for production

---

## 📊 System Architecture

```
Remote Devices (Offline-First)
    ↓ [sync when online]
Local Device Databases (SQLite)
    ↓ [sync queue]
Cloud Server (192.168.0.101:8001)
    ├─ Cloud Database (SQLite/MySQL)
    ├─ Admin Dashboard (/admin)
    ├─ Cloud Sync API (/api/sync/upload)
    ├─ Public Device Map (/devices)
    └─ Reporting (/cloud)
```

---

## ✅ Deployment Checklist Summary

### Pre-Deployment
- [ ] Target machine OS verified (Ubuntu/Debian)
- [ ] SSH access tested
- [ ] Target IP/hostname noted
- [ ] Current database backed up

### Deployment
- [ ] Run: `bash deploy.sh TARGET_IP ubuntu /home/ubuntu/cbe`
- [ ] Wait for completion (10-15 minutes)
- [ ] Check for success message

### Post-Deployment
- [ ] Access `http://TARGET_IP:8001` in browser
- [ ] Test student login (student1/student123)
- [ ] Test admin dashboard
- [ ] Check public device map (`/devices`)
- [ ] Verify database: `sqlite3 database.sqlite "SELECT COUNT(*) FROM users;"`

### Optional: Database Migration
- [ ] Run: `bash migrate-database.sh TARGET_IP ubuntu /home/ubuntu/cbe`
- [ ] Verify migrated data: Login with existing credentials
- [ ] Check database: `SELECT COUNT(*) FROM users;`

---

## 🎉 Ready to Deploy?

**Choose your path:**

1. **Fast & Easy:** `QUICKSTART_DEPLOYMENT.md`
2. **Detailed:** `DEPLOYMENT_GUIDE.md`
3. **Step-by-Step:** `DEPLOYMENT_CHECKLIST.md`

**Then run:**
```bash
cd /home/tele/cbe-platform
bash deploy.sh YOUR_TARGET_IP ubuntu /path/to/deploy
```

**Questions?** Check the documentation files above.

---

**Current System:** 192.168.0.100:8001 ✅ Running  
**Ready for Deployment:** ✅ Yes  
**Last Updated:** 2026-06-27

