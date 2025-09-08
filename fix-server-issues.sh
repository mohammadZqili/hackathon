#!/bin/bash

# Quick Fix Script for HTTP/HTTPS Development Server Issues
# Run this script to fix the localhost:8000 connection issues

echo "🔧 Fixing HTTP/HTTPS Development Server Issues..."
echo "=================================================="

# Navigate to project directory
cd ~/projects/hakathons/projects/guacpanel-tailwind-1.14

echo "✅ Step 1: Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "✅ Step 2: Verifying .env configuration..."
echo "Current APP_URL: $(grep APP_URL .env)"

echo "✅ Step 3: Current configuration cleared!"
echo ""
echo "🚀 Next Steps:"
echo "1. Start your development server with:"
echo "   php artisan serve --host=0.0.0.0 --port=8000"
echo ""
echo "2. Access your application at:"
echo "   http://localhost:8000"
echo ""
echo "3. Make sure your browser requests use HTTP (not HTTPS)"
echo ""
echo "❌ DO NOT access: https://localhost:8000 (will fail)"
echo "✅ DO access: http://localhost:8000 (will work)"
echo ""
echo "📝 If you still have issues:"
echo "1. Clear your browser cache for localhost"
echo "2. Try incognito/private browsing mode"
echo "3. Check browser console for any errors"
echo ""
echo "🎉 Fix completed! Your server should now work properly."
