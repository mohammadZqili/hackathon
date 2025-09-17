#!/bin/bash

echo "========================================="
echo "GuacPanel Docker Deployment Script"
echo "========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
IMAGE_NAME="guacpanel-app"
IMAGE_TAG="latest"
REGISTRY_URL="" # Set this if using a private registry
EXPORT_FILE="guacpanel-docker.tar.gz"

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --registry)
            REGISTRY_URL="$2"
            shift 2
            ;;
        --tag)
            IMAGE_TAG="$2"
            shift 2
            ;;
        --export)
            EXPORT_MODE=true
            shift
            ;;
        --import)
            IMPORT_MODE=true
            IMPORT_FILE="$2"
            shift 2
            ;;
        --help)
            echo "Usage: ./deploy-docker.sh [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --export              Export Docker images to a tar.gz file"
            echo "  --import FILE         Import Docker images from a tar.gz file"
            echo "  --registry URL        Push to a Docker registry"
            echo "  --tag TAG            Tag for the Docker image (default: latest)"
            echo "  --help               Show this help message"
            echo ""
            echo "Examples:"
            echo "  ./deploy-docker.sh --export"
            echo "  ./deploy-docker.sh --import guacpanel-docker.tar.gz"
            echo "  ./deploy-docker.sh --registry docker.io/username --tag v1.0.0"
            exit 0
            ;;
        *)
            echo -e "${RED}Unknown option: $1${NC}"
            exit 1
            ;;
    esac
done

# Export mode - Create a portable Docker image archive
if [ "$EXPORT_MODE" = true ]; then
    echo -e "${YELLOW}Exporting Docker images...${NC}"

    # Build the images first
    echo -e "${YELLOW}Building Docker images...${NC}"
    docker-compose build

    # Save all required images
    echo -e "${YELLOW}Creating Docker image archive...${NC}"
    docker save \
        guacpanel-app:latest \
        mysql:8.0 \
        redis:7-alpine \
        typesense/typesense:0.25.0 \
        phpmyadmin/phpmyadmin \
        -o guacpanel-images.tar

    # Create deployment package
    echo -e "${YELLOW}Creating deployment package...${NC}"
    tar -czf $EXPORT_FILE \
        docker-compose.yml \
        .env.docker.example \
        docker/ \
        guacpanel-images.tar \
        deploy-docker.sh \
        start-production.sh

    # Clean up temporary files
    rm guacpanel-images.tar

    # Calculate package size
    SIZE=$(du -h $EXPORT_FILE | cut -f1)

    echo -e "${GREEN}✓ Deployment package created: $EXPORT_FILE ($SIZE)${NC}"
    echo ""
    echo "To deploy on another machine:"
    echo "1. Copy $EXPORT_FILE to the target machine"
    echo "2. Extract: tar -xzf $EXPORT_FILE"
    echo "3. Import: ./deploy-docker.sh --import guacpanel-images.tar"
    echo "4. Start: docker-compose up -d"
    exit 0
fi

# Import mode - Load Docker images from archive
if [ "$IMPORT_MODE" = true ]; then
    if [ ! -f "$IMPORT_FILE" ]; then
        echo -e "${RED}Import file not found: $IMPORT_FILE${NC}"
        exit 1
    fi

    echo -e "${YELLOW}Importing Docker images from $IMPORT_FILE...${NC}"
    docker load -i $IMPORT_FILE

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Docker images imported successfully${NC}"
        echo ""
        echo "Next steps:"
        echo "1. Copy .env.docker.example to .env and configure"
        echo "2. Run: docker-compose up -d"
        echo "3. Access application at http://localhost:8080"
    else
        echo -e "${RED}Failed to import Docker images${NC}"
        exit 1
    fi
    exit 0
fi

# Registry mode - Push to Docker registry
if [ -n "$REGISTRY_URL" ]; then
    echo -e "${YELLOW}Pushing to registry: $REGISTRY_URL${NC}"

    # Build the image
    echo -e "${YELLOW}Building Docker image...${NC}"
    docker-compose build app

    # Tag the image
    FULL_IMAGE_NAME="$REGISTRY_URL/$IMAGE_NAME:$IMAGE_TAG"
    docker tag guacpanel-app:latest $FULL_IMAGE_NAME

    # Push to registry
    echo -e "${YELLOW}Pushing image to registry...${NC}"
    docker push $FULL_IMAGE_NAME

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Image pushed successfully: $FULL_IMAGE_NAME${NC}"
        echo ""
        echo "To deploy from registry on another machine:"
        echo "1. Update docker-compose.yml with image: $FULL_IMAGE_NAME"
        echo "2. Run: docker-compose pull"
        echo "3. Run: docker-compose up -d"
    else
        echo -e "${RED}Failed to push to registry${NC}"
        exit 1
    fi
    exit 0
fi

# Default - Show deployment information
echo -e "${BLUE}Deployment Options:${NC}"
echo ""
echo "1. Export for offline deployment:"
echo "   ./deploy-docker.sh --export"
echo ""
echo "2. Push to Docker registry:"
echo "   ./deploy-docker.sh --registry docker.io/username --tag v1.0.0"
echo ""
echo "3. Import from archive:"
echo "   ./deploy-docker.sh --import guacpanel-docker.tar.gz"
echo ""
echo "For more options: ./deploy-docker.sh --help"