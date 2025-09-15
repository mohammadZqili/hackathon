#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}Setting up Super Admin Permissions${NC}"
echo -e "${YELLOW}========================================${NC}"

# Run the permissions seeder
echo -e "${GREEN}Step 1: Running permissions seeder...${NC}"
php artisan db:seed --class=RolesAndPermissionsSeeder

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Permissions and roles created successfully!${NC}"
else
    echo -e "${RED}❌ Error running permissions seeder${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}Super Admin Setup Complete!${NC}"
echo -e "${YELLOW}========================================${NC}"

echo ""
echo -e "${GREEN}The System Admin role now has ALL permissions including:${NC}"
echo "• Full system control and management"
echo "• User, role, and permission management"
echo "• Complete hackathon management"
echo "• Database and backup management"
echo "• Security and audit log access"
echo "• Data import/export capabilities"
echo "• API and integration management"
echo "• User impersonation"
echo "• And much more..."

echo ""
echo -e "${YELLOW}To grant super admin privileges to a user, run:${NC}"
echo -e "${GREEN}php artisan user:make-super-admin user@example.com${NC}"

echo ""
echo -e "${YELLOW}Available commands:${NC}"
echo "• php artisan user:make-super-admin {email} - Grant super admin privileges"
echo "• php artisan permission:show - List all permissions"
echo "• php artisan role:show - List all roles"

echo ""
echo -e "${GREEN}✅ Setup complete!${NC}"
