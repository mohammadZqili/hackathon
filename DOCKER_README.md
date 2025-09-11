# GuacPanel Hackathon Management System - Docker Setup

This repository contains a complete Docker setup for the GuacPanel Hackathon Management System, making it easy to deploy and run the application anywhere.

## 🚀 Quick Start

### Prerequisites
- Docker Engine 20.10+
- Docker Compose 2.0+
- At least 4GB RAM available for containers

### 1. Clone and Prepare

```bash
git clone <your-repository-url>
cd guacpanel-tailwind-1.14
```

### 2. Environment Configuration

Copy the Docker environment template:
```bash
cp .env.docker .env
```

Edit the `.env` file with your configuration:
```bash
nano .env
```

**Important**: Update these variables:
- `APP_KEY` (will be generated automatically on first run)
- `TYPESENSE_API_KEY` and `TYPESENSE_SEARCH_ONLY_KEY`
- Mail configuration (SMTP settings)
- Any OAuth keys if needed

### 3. Build and Run

For production:
```bash
docker-compose up -d --build
```

For development:
```bash
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build
```

### 4. Access the Application

- **Main Application**: http://localhost:8000
- **MailPit (Email testing)**: http://localhost:8025
- **phpMyAdmin** (dev only): http://localhost:8080
- **Redis Commander** (dev only): http://localhost:8081

## 📦 Services Included

| Service | Description | Port | Status |
|---------|-------------|------|--------|
| **app** | Laravel + Vue.js Application | 8000 | Production Ready |
| **mysql** | MySQL 8.0 Database | 3306 | Production Ready |
| **redis** | Redis Cache & Sessions | 6379 | Production Ready |
| **typesense** | Search Engine | 8108 | Production Ready |
| **mailpit** | Email Testing | 8025 | Development |
| **phpmyadmin** | Database Admin | 8080 | Development Only |
| **redis-commander** | Redis Admin | 8081 | Development Only |

## 🛠️ Docker Commands

### Basic Operations
```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f app

# Restart a specific service
docker-compose restart app

# Execute commands in container
docker-compose exec app php artisan migrate
docker-compose exec app php artisan queue:work
```

### Maintenance
```bash
# Clear all caches
docker-compose exec app php artisan optimize:clear

# Run migrations
docker-compose exec app php artisan migrate

# Create admin user
docker-compose exec app php artisan db:seed --class=AdminSeeder

# Backup database
docker-compose exec mysql mysqldump -u guacpanel_user -pguacpanel_pass guacpanel > backup.sql
```

## 🔧 Configuration

### Environment Variables

Create a `.env` file based on `.env.docker`:

```env
APP_NAME="GuacPanel Hackathon"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=guacpanel
DB_USERNAME=guacpanel_user
DB_PASSWORD=guacpanel_pass

REDIS_HOST=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

TYPESENSE_API_KEY=your_secure_api_key_here
TYPESENSE_SEARCH_ONLY_KEY=your_search_key_here
```

### File Upload Limits

The Docker setup is configured for:
- Maximum file size: 15MB per file
- Maximum files: 8 files per idea
- Total post size: 20MB

### Performance Tuning

For production, consider:
- Increasing MySQL memory allocation
- Adjusting PHP-FPM worker processes
- Configuring Redis memory settings
- Setting up external CDN for assets

## 🔐 Security

### Production Security Checklist

- [ ] Change all default passwords
- [ ] Generate new API keys for Typesense
- [ ] Configure HTTPS with SSL certificates
- [ ] Set up firewall rules
- [ ] Configure backup strategy
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper mail settings

### Secrets Management

For production, use Docker secrets or environment variable injection:

```bash
echo "your_secret_key" | docker secret create app_key -
```

## 📊 Monitoring & Logs

### Log Locations
```bash
# Application logs
docker-compose logs app

# MySQL logs
docker-compose logs mysql

# Redis logs
docker-compose logs redis

# Nginx access logs
docker-compose exec app tail -f /var/log/nginx/access.log
```

### Health Checks

The application includes health check endpoints:
- `/health` - Application health
- `/up` - Simple uptime check

## 🚀 Deployment

### Production Deployment

1. **Server Requirements**:
   - 4+ CPU cores
   - 8GB+ RAM
   - 100GB+ SSD storage
   - Ubuntu 22.04 LTS

2. **Deploy Steps**:
```bash
# Clone repository
git clone <repo-url> /opt/guacpanel
cd /opt/guacpanel

# Set up environment
cp .env.docker .env
nano .env  # Configure production settings

# Deploy
docker-compose up -d --build

# Initialize database
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
```

3. **Configure Reverse Proxy** (Nginx):
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    
    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Update Deployment

```bash
# Pull latest changes
git pull origin main

# Rebuild and restart
docker-compose up -d --build

# Run migrations if needed
docker-compose exec app php artisan migrate --force
```

## 🔄 Backup & Restore

### Automated Backup Script

```bash
#!/bin/bash
# backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
docker-compose exec mysql mysqldump -u guacpanel_user -pguacpanel_pass guacpanel > backup_${DATE}.sql
docker-compose exec app tar -czf uploads_${DATE}.tar.gz /var/www/html/storage/app/
```

### Restore from Backup

```bash
# Restore database
docker-compose exec -i mysql mysql -u guacpanel_user -pguacpanel_pass guacpanel < backup.sql

# Restore uploads
docker-compose exec app tar -xzf uploads_backup.tar.gz -C /
```

## 🐛 Troubleshooting

### Common Issues

1. **Permission Issues**:
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

2. **Database Connection Issues**:
```bash
# Check MySQL status
docker-compose exec mysql mysql -u root -p -e "SHOW DATABASES;"

# Reset database
docker-compose down
docker volume rm guacpanel-tailwind-114_mysql_data
docker-compose up -d
```

3. **Memory Issues**:
   - Increase Docker Desktop memory allocation
   - Monitor container resource usage: `docker stats`

4. **Build Issues**:
```bash
# Clean rebuild
docker-compose down
docker system prune -a
docker-compose up -d --build --force-recreate
```

### Log Analysis

```bash
# Check all container logs
docker-compose logs

# Follow specific service logs
docker-compose logs -f app

# Check error logs
docker-compose exec app tail -f /var/www/html/storage/logs/laravel.log
```

## 📞 Support

For issues related to:
- **Docker setup**: Check this README and Docker documentation
- **Application features**: Refer to the main project documentation
- **Performance**: Review the monitoring section above

## 📄 License

This Docker configuration is provided under the same license as the main project.
