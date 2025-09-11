# GuacPanel Docker Quick Reference

## 🚀 Quick Start Commands

```bash
# 1. Initial setup
cp .env.docker .env
./deploy.sh

# 2. Start services
docker-compose up -d

# 3. Stop services
docker-compose down
```

## 📋 Daily Operations

### Application Management
```bash
# View application logs
docker-compose logs -f app

# Access application container
docker-compose exec app bash

# Clear Laravel caches
docker-compose exec app php artisan optimize:clear

# Run migrations
docker-compose exec app php artisan migrate

# Create admin user
docker-compose exec app php artisan make:admin

# Run queue worker
docker-compose exec app php artisan queue:work
```

### Database Operations
```bash
# Access MySQL
docker-compose exec mysql mysql -u guacpanel_user -pguacpanel_pass guacpanel

# Run database backup
./backup.sh

# Restore database
docker-compose exec -i mysql mysql -u guacpanel_user -pguacpanel_pass guacpanel < backup.sql

# View database logs
docker-compose logs mysql
```

### File Management
```bash
# View uploaded files
docker-compose exec app ls -la /var/www/html/storage/app/

# Copy files to container
docker cp ./file.txt guacpanel-app:/var/www/html/storage/app/

# Copy files from container
docker cp guacpanel-app:/var/www/html/storage/app/file.txt ./
```

## 🔧 Development Commands

```bash
# Start in development mode
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# Install new PHP package
docker-compose exec app composer require package/name

# Install new Node package
docker-compose exec app npm install package-name

# Run tests
docker-compose exec app php artisan test

# Generate IDE helper files
docker-compose exec app php artisan ide-helper:generate
```

## 📊 Monitoring

```bash
# Check container status
docker-compose ps

# Monitor resource usage
docker stats

# View all logs
docker-compose logs

# Follow specific service logs
docker-compose logs -f [service-name]

# Check container health
docker-compose exec app php artisan health:check
```

## 🔍 Troubleshooting

### Permission Issues
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

### Cache Issues
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Complete Reset
```bash
docker-compose down
docker volume prune
docker-compose up -d --build --force-recreate
```

### Service-specific Restart
```bash
# Restart application only
docker-compose restart app

# Restart database only
docker-compose restart mysql

# Restart search engine
docker-compose restart typesense
```

## 🔐 Production Security

### SSL Setup (with Nginx reverse proxy)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Generate certificate
sudo certbot --nginx -d yourdomain.com

# Auto-renewal test
sudo certbot renew --dry-run
```

### Security Hardening
```bash
# Change default passwords
docker-compose exec mysql mysql -u root -p -e "ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_strong_password';"

# Check for security updates
docker-compose pull
docker-compose up -d --build
```

## 📦 Backup & Restore

### Automated Backup
```bash
# Run backup script
./backup.sh

# Schedule backup (crontab -e)
0 2 * * * /path/to/guacpanel/backup.sh
```

### Manual Backup
```bash
# Database only
docker-compose exec mysql mysqldump -u guacpanel_user -pguacpanel_pass guacpanel > backup.sql

# Files only
docker-compose exec app tar -czf uploads.tar.gz /var/www/html/storage/app/
```

### Restore Process
```bash
# Stop services
docker-compose down

# Restore database
docker-compose up -d mysql
docker-compose exec -i mysql mysql -u guacpanel_user -pguacpanel_pass guacpanel < backup.sql

# Restore files
docker-compose up -d app
docker-compose exec app tar -xzf uploads.tar.gz -C /

# Start all services
docker-compose up -d
```

## 🌐 Environment Management

### Production Environment
```bash
# Deploy to production
APP_ENV=production docker-compose up -d --build

# Enable maintenance mode
docker-compose exec app php artisan down

# Disable maintenance mode
docker-compose exec app php artisan up
```

### Environment Variables
```bash
# View current environment
docker-compose exec app php artisan env

# Update configuration
docker-compose exec app php artisan config:cache
```

## 📱 Mobile & API Testing

### API Testing
```bash
# Test API endpoints
curl -X GET http://localhost:8000/api/health

# Generate API documentation
docker-compose exec app php artisan l5-swagger:generate
```

### QR Code Testing
```bash
# Generate test QR codes
docker-compose exec app php artisan qr:generate

# Test barcode scanning
curl -X POST http://localhost:8000/api/scan-attendance -d '{"barcode":"test123"}'
```

## 🔄 Updates & Maintenance

### Update Application
```bash
# Pull latest code
git pull origin main

# Rebuild containers
docker-compose up -d --build

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear caches
docker-compose exec app php artisan optimize:clear
```

### System Maintenance
```bash
# Clean Docker system
docker system prune -a

# Update base images
docker-compose pull
docker-compose up -d --build

# Check disk usage
docker system df
```

---

## 📞 Getting Help

- **Application Issues**: Check `/var/www/html/storage/logs/laravel.log`
- **Database Issues**: Check `docker-compose logs mysql`
- **Performance Issues**: Run `docker stats` to check resource usage
- **Permission Issues**: Run the permission fix commands above

For more detailed information, see `DOCKER_README.md`.
