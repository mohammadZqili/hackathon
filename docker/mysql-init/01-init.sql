-- Create database if not exists
CREATE DATABASE IF NOT EXISTS guacpanel;

-- Grant permissions to user
GRANT ALL PRIVILEGES ON guacpanel.* TO 'guacpanel'@'%';
FLUSH PRIVILEGES;

-- Set default character set
ALTER DATABASE guacpanel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;