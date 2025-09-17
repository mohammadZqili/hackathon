#!/bin/bash

echo "========================================="
echo "GuacPanel Docker Build Script"
echo "========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Docker Compose is not installed. Please install Docker Compose first.${NC}"
    exit 1
fi

# Stop any running containers
echo -e "${YELLOW}Stopping any existing containers...${NC}"
docker-compose down 2>/dev/null

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file from template...${NC}"
    cp .env.docker.example .env

    # Generate app key
    APP_KEY=$(openssl rand -base64 32)
    sed -i "s|base64:YOUR_APP_KEY_HERE|base64:${APP_KEY}|g" .env
    echo -e "${GREEN}.env file created with generated APP_KEY${NC}"
else
    echo -e "${GREEN}.env file already exists${NC}"
fi

# Build the Docker image
echo -e "${YELLOW}Building Docker images...${NC}"
docker-compose build --no-cache

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Docker images built successfully!${NC}"
else
    echo -e "${RED}Failed to build Docker images${NC}"
    exit 1
fi

# Start the containers
echo -e "${YELLOW}Starting containers...${NC}"
docker-compose up -d

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Containers started successfully!${NC}"
else
    echo -e "${RED}Failed to start containers${NC}"
    exit 1
fi

# Wait for services to be ready
echo -e "${YELLOW}Waiting for services to initialize...${NC}"
sleep 15

# Check if application is running
echo -e "${YELLOW}Checking application status...${NC}"
if curl -f http://localhost:8080 &>/dev/null; then
    echo -e "${GREEN}Application is running!${NC}"
else
    echo -e "${YELLOW}Application might still be initializing. Check logs with: docker-compose logs app${NC}"
fi

echo ""
echo "========================================="
echo -e "${GREEN}GuacPanel Docker Setup Complete!${NC}"
echo "========================================="
echo ""
echo "Access the application at: http://localhost:8080"
echo "Access phpMyAdmin at: http://localhost:8081"
echo ""
echo "Default credentials:"
echo "  Database: guacpanel / secret123"
echo "  phpMyAdmin: guacpanel / secret123"
echo ""
echo "Useful commands:"
echo "  View logs: docker-compose logs -f"
echo "  Stop containers: docker-compose down"
echo "  Restart containers: docker-compose restart"
echo "  Access app shell: docker-compose exec app sh"
echo "  Run artisan commands: docker-compose exec app php artisan [command]"
echo ""
echo "To seed the database with sample data:"
echo "  docker-compose exec app php artisan db:seed"
echo ""