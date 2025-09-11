-- MySQL initialization script for GuacPanel
-- This script runs automatically when the MySQL container starts for the first time

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Ensure the database exists and uses proper charset
ALTER DATABASE guacpanel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create additional indexes for better performance (Laravel migrations will create the tables)
-- This file can be extended with any custom SQL needed for the application
