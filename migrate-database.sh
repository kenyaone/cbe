#!/bin/bash

# Database Migration Script - Export current DB and import to new server
# Usage: bash migrate-database.sh <target-ip> <target-user> <target-path>

set -e

CURRENT_DB="/home/tele/cbe-platform/database/database.sqlite"
TARGET_IP=${1:-"192.168.0.101"}
TARGET_USER=${2:-"ubuntu"}
TARGET_PATH=${3:-"/home/ubuntu/cbe"}
BACKUP_DIR="/tmp/cbe-backups"

echo "================================================"
echo "CBE Platform Database Migration"
echo "================================================"

# Verify current database exists
if [ ! -f "$CURRENT_DB" ]; then
    echo "❌ Current database not found at $CURRENT_DB"
    exit 1
fi

echo "✅ Current database found"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Export database
echo "[1/3] Exporting current database..."
DUMP_FILE="$BACKUP_DIR/cbe_database_$(date +%Y%m%d_%H%M%S).sql"
sqlite3 "$CURRENT_DB" ".dump" > "$DUMP_FILE"
echo "✅ Database exported to $DUMP_FILE"

# Transfer to target
echo "[2/3] Transferring database to target server..."
scp "$DUMP_FILE" "$TARGET_USER@$TARGET_IP:/tmp/cbe_database.sql"
echo "✅ Database transferred"

# Import on target
echo "[3/3] Importing database on target server..."
ssh "$TARGET_USER@$TARGET_IP" << IMPORT_EOF
cd $TARGET_PATH

# Backup existing database if it exists
if [ -f database/database.sqlite ]; then
    cp database/database.sqlite database/database.sqlite.backup
    echo "  Backed up existing database"
fi

# Import new database
sqlite3 database/database.sqlite < /tmp/cbe_database.sql
chmod 664 database/database.sqlite
rm /tmp/cbe_database.sql

# Verify import
COUNT=\$(sqlite3 database/database.sqlite "SELECT COUNT(*) FROM users;")
echo "  ✅ Import successful - \$COUNT users found"

IMPORT_EOF

echo ""
echo "================================================"
echo "✅ Database Migration Complete!"
echo "================================================"
echo ""
echo "Backup saved at: $DUMP_FILE"
echo ""
echo "Next steps:"
echo "1. Verify data on target: ssh $TARGET_USER@$TARGET_IP"
echo "2. Check users: sqlite3 $TARGET_PATH/database/database.sqlite 'SELECT * FROM users LIMIT 5;'"
echo "3. Test login with: student1 / student123"
echo ""
