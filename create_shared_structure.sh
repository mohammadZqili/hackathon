#!/bin/bash

# Create shared Vue pages structure
mkdir -p resources/js/Pages/Shared/{Teams,Ideas,Tracks,Workshops,News,Dashboard}

# Create symlinks for each role to use shared pages
mkdir -p resources/js/Pages/HackathonAdmin
mkdir -p resources/js/Pages/TrackSupervisor

# Copy existing SystemAdmin pages to Shared
cp -r resources/js/Pages/SystemAdmin/Teams/* resources/js/Pages/Shared/Teams/ 2>/dev/null || true
cp -r resources/js/Pages/SystemAdmin/Ideas/* resources/js/Pages/Shared/Ideas/ 2>/dev/null || true

echo "Shared structure created successfully"
