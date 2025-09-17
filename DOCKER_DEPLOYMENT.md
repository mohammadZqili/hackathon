# GuacPanel Docker Deployment Guide

## Overview
This guide explains how to deploy GuacPanel using Docker. The Docker setup includes all necessary services: Laravel application, MySQL database, Redis cache, Typesense search, and phpMyAdmin for database management.

## Prerequisites
- Docker Engine (version 20.10 or higher)
- Docker Compose (version 1.29 or higher)
- At least 4GB of available RAM
- 10GB of free disk space

## Quick Start

### 1. Build and Start Everything
```bash
# Run the automated build script
./build-docker.sh
```

This script will:
- Check Docker installation
- Create `.env` file with generated APP_KEY
- Build Docker images
- Start all containers
- Initialize the database

### 2. Access the Application
- **Main Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Username: `guacpanel`
  - Password: `secret123`

## Manual Deployment

### Step 1: Configure Environment
```bash
# Copy environment template
cp .env.docker.example .env

# Edit .env file with your settings
nano .env
```

Key settings to configure:
- `APP_URL`: Your application URL
- `DB_PASSWORD`: Database password (change from default)
- `MAIL_*`: Email configuration for notifications
- `TYPESENSE_API_KEY`: Search engine API key

### Step 2: Build and Start Containers
```bash
# Build the Docker images
docker-compose build

# Start containers in detached mode
docker-compose up -d

# Check container status
docker-compose ps
```

### Step 3: Initialize Database
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Seed database with sample data (optional)
docker-compose exec app php artisan db:seed
```

## Deployment Options

### Option 1: Export for Offline Deployment
Create a portable package that can be deployed without internet access:

```bash
# Create deployment package
./deploy-docker.sh --export

# This creates guacpanel-docker.tar.gz
# Transfer this file to the target machine
```

On the target machine:
```bash
# Extract the package
tar -xzf guacpanel-docker.tar.gz

# Import Docker images
./deploy-docker.sh --import guacpanel-images.tar

# Start the application
docker-compose up -d
```

### Option 2: Push to Docker Registry
Deploy using a Docker registry (Docker Hub, AWS ECR, etc.):

```bash
# Push to registry
./deploy-docker.sh --registry docker.io/yourusername --tag v1.0.0

# On target machine, update docker-compose.yml with:
# image: docker.io/yourusername/guacpanel-app:v1.0.0

# Pull and start
docker-compose pull
docker-compose up -d
```

### Option 3: Direct Server Deployment
Deploy directly to a server with Docker installed:

```bash
# On the server, clone the repository
git clone <repository-url>
cd guacpanel-tailwind-1.14

# Run the build script
./build-docker.sh

# Or use production startup script
./start-production.sh
```

## Service Architecture

### Container Services
1. **app** (Port 8080)
   - Laravel application with Nginx and PHP-FPM
   - Queue workers and task scheduler

2. **mysql** (Port 3307)
   - MySQL 8.0 database
   - Persistent data volume

3. **redis** (Port 6380)
   - Redis cache and session storage
   - Persistent data volume

4. **typesense** (Port 8108)
   - Search engine service
   - Persistent data volume

5. **phpmyadmin** (Port 8081)
   - Database management interface

## Data Persistence

Docker volumes ensure data persistence:
- `mysql-data`: Database files
- `redis-data`: Cache data
- `typesense-data`: Search indices
- `./storage/app`: Application uploads
- `./storage/logs`: Application logs

## Common Management Commands

### Container Management
```bash
# View logs
docker-compose logs -f            # All services
docker-compose logs -f app         # Application only

# Restart services
docker-compose restart             # All services
docker-compose restart app         # Application only

# Stop services
docker-compose down               # Stop and remove containers
docker-compose down -v            # Also remove volumes (WARNING: deletes data)

# Access container shell
docker-compose exec app sh        # Application container
docker-compose exec mysql bash    # MySQL container
```

### Laravel Artisan Commands
```bash
# Run any artisan command
docker-compose exec app php artisan <command>

# Common commands
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan queue:restart
docker-compose exec app php artisan migrate:status
```

### Database Management
```bash
# Create database backup
docker-compose exec mysql mysqldump -u guacpanel -psecret123 guacpanel > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u guacpanel -psecret123 guacpanel < backup.sql

# Access MySQL CLI
docker-compose exec mysql mysql -u guacpanel -psecret123 guacpanel
```

## Production Considerations

### Security
1. **Change default passwords** in `.env` and `docker-compose.yml`
2. **Generate new APP_KEY**: `docker-compose exec app php artisan key:generate`
3. **Configure firewall** to restrict port access
4. **Use HTTPS** with a reverse proxy (Nginx, Traefik, etc.)
5. **Remove phpMyAdmin** in production or restrict access

### Performance Optimization
```bash
# Enable production optimizations
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan optimize
```

### Resource Limits
Add resource limits to `docker-compose.yml`:
```yaml
services:
  app:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
```

### Backup Strategy
```bash
# Automated backup script
#!/bin/bash
BACKUP_DIR="/backups/$(date +%Y%m%d)"
mkdir -p $BACKUP_DIR

# Backup database
docker-compose exec mysql mysqldump -u guacpanel -psecret123 guacpanel > $BACKUP_DIR/database.sql

# Backup uploads
tar -czf $BACKUP_DIR/uploads.tar.gz ./storage/app

# Backup environment
cp .env $BACKUP_DIR/.env
```

## Monitoring

### Health Checks
```bash
# Check application health
curl http://localhost:8080/health

# Check container status
docker-compose ps

# Check resource usage
docker stats
```

### Logging
```bash
# Application logs
tail -f storage/logs/laravel.log

# Container logs
docker-compose logs -f --tail=100

# Nginx access logs
docker-compose exec app tail -f /var/log/nginx/access.log
```

## Troubleshooting

### Common Issues

#### 1. Port Already in Use
```bash
# Change ports in docker-compose.yml
ports:
  - "8081:80"  # Change 8080 to 8081
```

#### 2. Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

#### 3. Database Connection Failed
```bash
# Check MySQL is running
docker-compose ps mysql

# Test connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

#### 4. Memory Issues
```bash
# Increase Docker memory allocation
# Docker Desktop: Settings > Resources > Memory

# Or add swap space on Linux
sudo fallocate -l 4G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
```

## Updates and Maintenance

### Update Application
```bash
# Pull latest code
git pull origin main

# Rebuild images
docker-compose build

# Run migrations
docker-compose exec app php artisan migrate

# Restart services
docker-compose down && docker-compose up -d
```

### Clean Up
```bash
# Remove unused images
docker image prune -a

# Remove unused volumes
docker volume prune

# Full cleanup (WARNING: removes all Docker data)
docker system prune -a --volumes
```

## Support

For issues or questions:
1. Check the logs: `docker-compose logs -f`
2. Review this documentation
3. Check the main README.md
4. Open an issue on the project repository

## License

This Docker deployment setup is part of the GuacPanel project and follows the same license terms.