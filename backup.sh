#!/bin/bash

# GuacPanel Backup Script
# Automated backup for database and uploaded files

set -e

# Configuration
BACKUP_DIR="/opt/backups/guacpanel"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}[BACKUP]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Create backup directory
mkdir -p "$BACKUP_DIR"

print_status "Starting backup process..."

# Backup database
print_status "Backing up database..."
docker-compose exec -T mysql mysqldump \
    -u guacpanel_user \
    -pguacpanel_pass \
    --single-transaction \
    --routines \
    --triggers \
    guacpanel > "$BACKUP_DIR/database_${DATE}.sql"

if [ $? -eq 0 ]; then
    print_status "Database backup completed: database_${DATE}.sql"
    
    # Compress database backup
    gzip "$BACKUP_DIR/database_${DATE}.sql"
    print_status "Database backup compressed."
else
    print_error "Database backup failed!"
    exit 1
fi

# Backup uploaded files
print_status "Backing up uploaded files..."
docker-compose exec -T app tar -czf - /var/www/html/storage/app > "$BACKUP_DIR/uploads_${DATE}.tar.gz"

if [ $? -eq 0 ]; then
    print_status "Files backup completed: uploads_${DATE}.tar.gz"
else
    print_error "Files backup failed!"
    exit 1
fi

# Backup environment file
print_status "Backing up environment configuration..."
cp .env "$BACKUP_DIR/env_${DATE}.backup"

# Create backup manifest
cat > "$BACKUP_DIR/manifest_${DATE}.txt" << EOF
GuacPanel Backup Manifest
========================
Date: $(date)
Backup ID: ${DATE}

Files included:
- database_${DATE}.sql.gz (MySQL database dump)
- uploads_${DATE}.tar.gz (User uploaded files)
- env_${DATE}.backup (Environment configuration)

Restore commands:
# Database:
gunzip database_${DATE}.sql.gz
docker-compose exec -i mysql mysql -u guacpanel_user -pguacpanel_pass guacpanel < database_${DATE}.sql

# Files:
docker-compose exec -i app tar -xzf - -C / < uploads_${DATE}.tar.gz

# Environment:
cp env_${DATE}.backup .env
EOF

print_status "Backup manifest created: manifest_${DATE}.txt"

# Calculate backup sizes
DB_SIZE=$(du -h "$BACKUP_DIR/database_${DATE}.sql.gz" | cut -f1)
FILES_SIZE=$(du -h "$BACKUP_DIR/uploads_${DATE}.tar.gz" | cut -f1)
TOTAL_SIZE=$(du -sh "$BACKUP_DIR" | cut -f1)

print_status "Backup sizes:"
print_status "  Database: $DB_SIZE"
print_status "  Files: $FILES_SIZE"
print_status "  Total backup directory: $TOTAL_SIZE"

# Clean old backups
print_status "Cleaning old backups (older than $RETENTION_DAYS days)..."
find "$BACKUP_DIR" -name "database_*.sql.gz" -mtime +$RETENTION_DAYS -delete 2>/dev/null || true
find "$BACKUP_DIR" -name "uploads_*.tar.gz" -mtime +$RETENTION_DAYS -delete 2>/dev/null || true
find "$BACKUP_DIR" -name "env_*.backup" -mtime +$RETENTION_DAYS -delete 2>/dev/null || true
find "$BACKUP_DIR" -name "manifest_*.txt" -mtime +$RETENTION_DAYS -delete 2>/dev/null || true

# List recent backups
print_status "Recent backups:"
ls -la "$BACKUP_DIR" | tail -10

print_status "✅ Backup completed successfully!"
print_status "Backup location: $BACKUP_DIR"
print_status "Backup ID: $DATE"

# Optional: Upload to cloud storage
if [ ! -z "$BACKUP_S3_BUCKET" ]; then
    print_status "Uploading to S3..."
    aws s3 cp "$BACKUP_DIR/database_${DATE}.sql.gz" "s3://$BACKUP_S3_BUCKET/guacpanel/"
    aws s3 cp "$BACKUP_DIR/uploads_${DATE}.tar.gz" "s3://$BACKUP_S3_BUCKET/guacpanel/"
    print_status "Backup uploaded to S3."
fi

# Exit with success
exit 0
