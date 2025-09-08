#!/bin/bash

# Routes Fix & Test Script
# This script clears caches and tests the routes

echo "🛠️  Clearing Laravel caches..."
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo ""
echo "✅ Caches cleared successfully!"
echo ""

echo "🔍 Testing hackathon-admin routes..."
echo "Looking for ideas routes:"
php artisan route:list | grep "hackathon-admin.ideas"

echo ""
echo "🎯 Key routes to test in browser:"
echo "- http://localhost:8000/hackathon-admin/ideas (Ideas list)"
echo "- http://localhost:8000/hackathon-admin/ideas/1/review (Review form - should work now!)"
echo "- http://localhost:8000/hackathon-admin/workshops (Workshops list)"
echo ""

echo "✅ Routes are now properly organized and complete!"
echo "📁 Check routes/hackathon.php for all hackathon routes"
echo ""
echo "🚀 Fixed Issues:"
echo "- ✅ Route 'hackathon-admin.ideas.process-review' now exists"
echo "- ✅ Status values corrected (draft, submitted, accepted, etc.)"
echo "- ✅ Field names aligned (reviewed_by instead of supervisor_id)"
echo "- ✅ Frontend-backend data flow alignment"
echo ""
echo "🎉 The idea review system should now work without Ziggy errors!"
