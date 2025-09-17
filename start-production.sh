#!/bin/bash

echo "========================================="
echo "GuacPanel Production Startup"
echo "========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file from template...${NC}"
    cp .env.docker.example .env
    echo -e "${RED}Please edit .env file with your production settings before continuing${NC}"
    exit 1
fi

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}Docker is not running. Please start Docker first.${NC}"
    exit 1
fi

# Start containers in production mode
echo -e "${YELLOW}Starting production containers...${NC}"
docker-compose up -d

# Wait for containers to be ready
echo -e "${YELLOW}Waiting for services to initialize (30 seconds)...${NC}"
sleep 30

# Check container status
echo -e "${YELLOW}Checking container status...${NC}"
docker-compose ps

# Run production optimizations
echo -e "${YELLOW}Running production optimizations...${NC}"
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

# Check application health
echo -e "${YELLOW}Checking application health...${NC}"
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080)

if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" ]; then
    echo -e "${GREEN}✓ Application is running successfully!${NC}"
    echo ""
    echo "========================================="
    echo "GuacPanel is running in production mode!"
    echo "========================================="
    echo ""
    echo "Access points:"
    echo "  Application: http://localhost:8080"
    echo "  phpMyAdmin: http://localhost:8081"
    echo ""
    echo "Monitor logs:"
    echo "  All services: docker-compose logs -f"
    echo "  Application only: docker-compose logs -f app"
    echo ""
    echo "Management commands:"
    echo "  Stop: docker-compose down"
    echo "  Restart: docker-compose restart"
    echo "  Update: git pull && docker-compose build && docker-compose up -d"
    echo ""
else
    echo -e "${RED}Application health check failed (HTTP $HTTP_STATUS)${NC}"
    echo -e "${YELLOW}Check logs with: docker-compose logs app${NC}"
    exit 1
fi