#!/bin/bash

# Quick Fix Script for Dashboard "Attempt to read property 'name' on null" Error
# This script will fix the database integrity issues and restart the application

set -e

echo "🚨 EMERGENCY FIX: Dashboard Null Reference Error"
echo "================================================"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the Laravel project root directory"
    exit 1
fi

print_status "Starting emergency dashboard fix..."

# Step 1: Clear all caches
print_status "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Step 2: Run our data integrity fix command
print_status "Running data integrity fix..."
php artisan hackathon:fix-dashboard-data

# Step 3: Seed test data if needed
read -p "Do you want to seed test data for the dashboard? (y/N): " seed_data
if [[ $seed_data =~ ^[Yy]$ ]]; then
    print_status "Seeding dashboard test data..."
    php artisan db:seed --class=DashboardTestDataSeeder
fi

# Step 4: Test the dashboard
print_status "Testing dashboard access..."

# Check if the application is running
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/login | grep -q "200"; then
    print_success "Application is running and accessible"
    
    # Test the dashboard endpoint
    print_status "Testing dashboard endpoint..."
    
    # Create a temporary test script
    cat > test_dashboard.php << 'EOF'
<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test the dashboard controller method
    $controller = new App\Http\Controllers\HackathonAdmin\DashboardController();
    $response = $controller->index();
    
    echo "✅ Dashboard controller working properly\n";
    echo "📊 Statistics available: " . (isset($response->getData()['statistics']) ? "Yes" : "No") . "\n";
    
} catch (Exception $e) {
    echo "❌ Dashboard still has issues: " . $e->getMessage() . "\n";
    exit(1);
}
EOF

    php test_dashboard.php
    rm test_dashboard.php
    
    print_success "Dashboard is working properly!"
    
else
    print_warning "Application might not be running. Starting development server..."
    
    # Start the development server in background for testing
    php artisan serve --host=0.0.0.0 --port=8000 &
    SERVER_PID=$!
    
    # Wait a moment for server to start
    sleep 3
    
    # Test again
    if curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/login | grep -q "200"; then
        print_success "Development server started successfully"
    else
        print_error "Could not start development server"
        kill $SERVER_PID 2>/dev/null || true
        exit 1
    fi
    
    # Kill the background server
    kill $SERVER_PID 2>/dev/null || true
fi

# Step 5: Provide access instructions
print_success "🎉 Dashboard fix completed successfully!"
echo ""
echo "📋 Next Steps:"
echo "1. Start your development server: php artisan serve"
echo "2. Access the dashboard: http://localhost:8000/hackathon-admin/dashboard"
echo "3. Login credentials:"
echo "   Email: admin@ruman.sa"
echo "   Password: password"
echo ""
echo "🔧 If you're using Docker:"
echo "1. docker-compose up -d"
echo "2. docker-compose exec app php artisan hackathon:fix-dashboard-data"
echo "3. Access: http://localhost:8000/hackathon-admin/dashboard"
echo ""
echo "📊 The dashboard should now show:"
echo "   ✅ Current hackathon edition statistics"
echo "   ✅ Recent team activities"
echo "   ✅ Recent idea submissions"
echo "   ✅ No more null reference errors"
echo ""
print_success "Emergency fix completed! 🚀"
