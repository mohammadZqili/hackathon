#!/bin/bash

# GuacPanel Docker Deployment Script
# This script automates the deployment process

set -e

echo "🚀 GuacPanel Hackathon Management System - Docker Deployment"
echo "============================================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

print_success "Docker and Docker Compose are installed."

# Check if .env file exists
if [ ! -f ".env" ]; then
    print_warning ".env file not found. Creating from template..."
    cp .env.docker .env
    print_success ".env file created from template."
    print_warning "Please edit .env file with your configuration before continuing."
    read -p "Press Enter to continue after editing .env file..."
fi

# Deployment type selection
echo ""
echo "Select deployment type:"
echo "1) Production (recommended for live server)"
echo "2) Development (includes additional tools)"
read -p "Enter your choice (1-2): " deployment_type

case $deployment_type in
    1)
        COMPOSE_FILE="docker-compose.yml"
        ENVIRONMENT="production"
        ;;
    2)
        COMPOSE_FILE="docker-compose.yml -f docker-compose.dev.yml"
        ENVIRONMENT="development"
        ;;
    *)
        print_error "Invalid choice. Exiting."
        exit 1
        ;;
esac

print_status "Deploying in $ENVIRONMENT mode..."

# Stop existing containers
print_status "Stopping existing containers..."
docker-compose down 2>/dev/null || true

# Pull latest images
print_status "Pulling latest images..."
docker-compose -f docker-compose.yml pull 2>/dev/null || true

# Build and start containers
print_status "Building and starting containers..."
if [ "$deployment_type" = "2" ]; then
    docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build
else
    docker-compose up -d --build
fi

# Wait for services to be ready
print_status "Waiting for services to start..."
sleep 30

# Check if MySQL is ready
print_status "Waiting for MySQL to be ready..."
until docker-compose exec mysql mysqladmin ping -h localhost --silent; do
    printf '.'
    sleep 1
done
echo ""
print_success "MySQL is ready."

# Generate app key if needed
print_status "Checking application key..."
if ! docker-compose exec app php artisan key:generate --show | grep -q "base64:"; then
    docker-compose exec app php artisan key:generate --force
    print_success "Application key generated."
fi

# Run migrations
print_status "Running database migrations..."
docker-compose exec app php artisan migrate --force

# Create storage link
print_status "Creating storage symlink..."
docker-compose exec app php artisan storage:link

# Seed database (optional)
read -p "Do you want to run database seeders? (y/N): " seed_db
if [[ $seed_db =~ ^[Yy]$ ]]; then
    print_status "Running database seeders..."
    docker-compose exec app php artisan db:seed --force
    print_success "Database seeders completed."
fi

# Show service status
print_status "Checking service status..."
docker-compose ps

echo ""
print_success "🎉 Deployment completed successfully!"
echo ""
echo "📍 Access URLs:"
echo "   Main Application: http://localhost:8000"
echo "   MailPit (Email): http://localhost:8025"

if [ "$deployment_type" = "2" ]; then
    echo "   phpMyAdmin: http://localhost:8080"
    echo "   Redis Commander: http://localhost:8081"
fi

echo ""
echo "🔧 Useful commands:"
echo "   View logs: docker-compose logs -f app"
echo "   Stop services: docker-compose down"
echo "   Restart: docker-compose restart app"
echo ""

# Check for any failed containers
failed_containers=$(docker-compose ps --services --filter "status=exited")
if [ ! -z "$failed_containers" ]; then
    print_warning "Some containers failed to start properly:"
    echo "$failed_containers"
    print_warning "Check logs with: docker-compose logs [service-name]"
fi

print_success "Deployment script completed!"
