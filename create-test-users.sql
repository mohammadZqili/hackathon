-- Create Test Users for Role-Based Access Control Testing
-- Password for all users: password123

-- 1. System Admin User
INSERT INTO users (id, name, email, password, user_type, email_verified_at, created_at, updated_at) 
VALUES 
('9b123456-0001-0001-0001-000000000001', 'System Admin', 'system@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'system_admin', NOW(), NOW(), NOW());

-- 2. Hackathon Admin User (for Edition 1)
INSERT INTO users (id, name, email, password, user_type, email_verified_at, created_at, updated_at) 
VALUES 
('9b123456-0002-0002-0002-000000000002', 'Hackathon Admin 1', 'hackathon1@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hackathon_admin', NOW(), NOW(), NOW());

-- 3. Hackathon Admin User (for Edition 2)
INSERT INTO users (id, name, email, password, user_type, email_verified_at, created_at, updated_at) 
VALUES 
('9b123456-0003-0003-0003-000000000003', 'Hackathon Admin 2', 'hackathon2@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hackathon_admin', NOW(), NOW(), NOW());

-- 4. Track Supervisor User (for Track 1 in Edition 1)
INSERT INTO users (id, name, email, password, user_type, email_verified_at, created_at, updated_at) 
VALUES 
('9b123456-0004-0004-0004-000000000004', 'Track Supervisor 1', 'track1@supervisor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'track_supervisor', NOW(), NOW(), NOW());

-- 5. Track Supervisor User (for Track 2 in Edition 1)
INSERT INTO users (id, name, email, password, user_type, email_verified_at, created_at, updated_at) 
VALUES 
('9b123456-0005-0005-0005-000000000005', 'Track Supervisor 2', 'track2@supervisor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'track_supervisor', NOW(), NOW(), NOW());

-- 6. Track Supervisor User (for Track in Edition 2)
INSERT INTO users (id, name, email, password, user_type, email_verified_at, created_at, updated_at) 
VALUES 
('9b123456-0006-0006-0006-000000000006', 'Track Supervisor 3', 'track3@supervisor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'track_supervisor', NOW(), NOW(), NOW());

-- ============================================
-- Assign Roles using Spatie Permission
-- ============================================

-- First, ensure roles exist
INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES 
('system_admin', 'web', NOW(), NOW()),
('hackathon_admin', 'web', NOW(), NOW()),
('track_supervisor', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Assign roles to users
INSERT INTO model_has_roles (role_id, model_type, model_id) 
SELECT r.id, 'App\\Models\\User', '9b123456-0001-0001-0001-000000000001'
FROM roles r WHERE r.name = 'system_admin';

INSERT INTO model_has_roles (role_id, model_type, model_id) 
SELECT r.id, 'App\\Models\\User', '9b123456-0002-0002-0002-000000000002'
FROM roles r WHERE r.name = 'hackathon_admin';

INSERT INTO model_has_roles (role_id, model_type, model_id) 
SELECT r.id, 'App\\Models\\User', '9b123456-0003-0003-0003-000000000003'
FROM roles r WHERE r.name = 'hackathon_admin';

INSERT INTO model_has_roles (role_id, model_type, model_id) 
SELECT r.id, 'App\\Models\\User', '9b123456-0004-0004-0004-000000000004'
FROM roles r WHERE r.name = 'track_supervisor';

INSERT INTO model_has_roles (role_id, model_type, model_id) 
SELECT r.id, 'App\\Models\\User', '9b123456-0005-0005-0005-000000000005'
FROM roles r WHERE r.name = 'track_supervisor';

INSERT INTO model_has_roles (role_id, model_type, model_id) 
SELECT r.id, 'App\\Models\\User', '9b123456-0006-0006-0006-000000000006'
FROM roles r WHERE r.name = 'track_supervisor';

-- ============================================
-- Create Test Editions
-- ============================================

-- Edition 1 (Assigned to Hackathon Admin 1)
INSERT INTO editions (id, name, year, registration_start_date, registration_end_date, hackathon_start_date, hackathon_end_date, admin_id, description, location, max_teams, max_team_members, is_active, created_at, updated_at)
VALUES 
(1, 'Hackathon 2024 - Spring Edition', 2024, '2024-01-01', '2024-02-01', '2024-03-01', '2024-03-03', '9b123456-0002-0002-0002-000000000002', 'Spring edition of our annual hackathon', 'Riyadh, Saudi Arabia', 50, 5, 1, NOW(), NOW());

-- Edition 2 (Assigned to Hackathon Admin 2)
INSERT INTO editions (id, name, year, registration_start_date, registration_end_date, hackathon_start_date, hackathon_end_date, admin_id, description, location, max_teams, max_team_members, is_active, created_at, updated_at)
VALUES 
(2, 'Hackathon 2024 - Summer Edition', 2024, '2024-06-01', '2024-07-01', '2024-08-01', '2024-08-03', '9b123456-0003-0003-0003-000000000003', 'Summer edition of our annual hackathon', 'Jeddah, Saudi Arabia', 40, 4, 1, NOW(), NOW());

-- ============================================
-- Create Test Tracks
-- ============================================

-- Tracks for Edition 1
INSERT INTO tracks (id, name, description, edition_id, max_teams, created_at, updated_at)
VALUES 
(1, 'AI & Machine Learning', 'Track for AI and ML projects', 1, 20, NOW(), NOW()),
(2, 'Web Development', 'Track for web-based projects', 1, 15, NOW(), NOW()),
(3, 'Mobile Apps', 'Track for mobile applications', 1, 15, NOW(), NOW());

-- Tracks for Edition 2
INSERT INTO tracks (id, name, description, edition_id, max_teams, created_at, updated_at)
VALUES 
(4, 'Blockchain', 'Track for blockchain projects', 2, 10, NOW(), NOW()),
(5, 'IoT Solutions', 'Track for Internet of Things', 2, 15, NOW(), NOW()),
(6, 'Cybersecurity', 'Track for security solutions', 2, 15, NOW(), NOW());

-- ============================================
-- Assign Track Supervisors
-- ============================================

-- Track Supervisor 1 supervises AI & ML track in Edition 1
INSERT INTO track_supervisors (track_id, user_id, created_at, updated_at)
VALUES (1, '9b123456-0004-0004-0004-000000000004', NOW(), NOW());

-- Track Supervisor 2 supervises Web Development track in Edition 1
INSERT INTO track_supervisors (track_id, user_id, created_at, updated_at)
VALUES (2, '9b123456-0005-0005-0005-000000000005', NOW(), NOW());

-- Track Supervisor 3 supervises Blockchain track in Edition 2
INSERT INTO track_supervisors (track_id, user_id, created_at, updated_at)
VALUES (4, '9b123456-0006-0006-0006-000000000006', NOW(), NOW());

-- ============================================
-- Create Sample Teams for Testing
-- ============================================

-- Teams in Edition 1, Track 1 (AI & ML)
INSERT INTO teams (id, name, description, edition_id, track_id, leader_id, status, created_at, updated_at)
VALUES 
('9b223456-1001-1001-1001-000000000001', 'AI Innovators', 'Working on AI solution', 1, 1, '9b123456-0004-0004-0004-000000000004', 'active', NOW(), NOW()),
('9b223456-1002-1002-1002-000000000002', 'ML Masters', 'Machine learning project', 1, 1, '9b123456-0004-0004-0004-000000000004', 'active', NOW(), NOW());

-- Teams in Edition 1, Track 2 (Web Dev)
INSERT INTO teams (id, name, description, edition_id, track_id, leader_id, status, created_at, updated_at)
VALUES 
('9b223456-1003-1003-1003-000000000003', 'Web Warriors', 'Web platform development', 1, 2, '9b123456-0005-0005-0005-000000000005', 'active', NOW(), NOW());

-- Teams in Edition 2, Track 4 (Blockchain)
INSERT INTO teams (id, name, description, edition_id, track_id, leader_id, status, created_at, updated_at)
VALUES 
('9b223456-2001-2001-2001-000000000001', 'Crypto Coders', 'Blockchain solution', 2, 4, '9b123456-0006-0006-0006-000000000006', 'active', NOW(), NOW()),
('9b223456-2002-2002-2002-000000000002', 'Chain Builders', 'DeFi platform', 2, 4, '9b123456-0006-0006-0006-000000000006', 'active', NOW(), NOW());

-- ============================================
-- Create Sample Ideas for Testing
-- ============================================

-- Ideas for Edition 1 teams
INSERT INTO ideas (id, title, description, team_id, status, created_at, updated_at)
VALUES 
('9b323456-1001-1001-1001-000000000001', 'AI-Powered Healthcare Assistant', 'Using AI to improve healthcare', '9b223456-1001-1001-1001-000000000001', 'submitted', NOW(), NOW()),
('9b323456-1002-1002-1002-000000000002', 'ML Stock Predictor', 'Machine learning for stock prediction', '9b223456-1002-1002-1002-000000000002', 'under_review', NOW(), NOW()),
('9b323456-1003-1003-1003-000000000003', 'E-Learning Platform', 'Innovative web-based learning', '9b223456-1003-1003-1003-000000000003', 'accepted', NOW(), NOW());

-- Ideas for Edition 2 teams
INSERT INTO ideas (id, title, description, team_id, status, created_at, updated_at)
VALUES 
('9b323456-2001-2001-2001-000000000001', 'Decentralized Identity System', 'Blockchain identity management', '9b223456-2001-2001-2001-000000000001', 'submitted', NOW(), NOW()),
('9b323456-2002-2002-2002-000000000002', 'Smart Contract Marketplace', 'DeFi marketplace platform', '9b223456-2002-2002-2002-000000000002', 'under_review', NOW(), NOW());

-- ============================================
-- Summary of Test Users and Access
-- ============================================
-- 
-- 1. System Admin (system@admin.com / password123)
--    - Can see ALL editions, tracks, teams, and ideas
--    - Full CRUD permissions on everything
-- 
-- 2. Hackathon Admin 1 (hackathon1@admin.com / password123)
--    - Can ONLY see Edition 1 (Spring Edition)
--    - Can see tracks 1, 2, 3 and their teams/ideas
--    - Cannot see Edition 2 data
-- 
-- 3. Hackathon Admin 2 (hackathon2@admin.com / password123)
--    - Can ONLY see Edition 2 (Summer Edition)
--    - Can see tracks 4, 5, 6 and their teams/ideas
--    - Cannot see Edition 1 data
-- 
-- 4. Track Supervisor 1 (track1@supervisor.com / password123)
--    - Can ONLY see Track 1 (AI & ML) in Edition 1
--    - Can see teams: AI Innovators, ML Masters
--    - Cannot see other tracks or editions
-- 
-- 5. Track Supervisor 2 (track2@supervisor.com / password123)
--    - Can ONLY see Track 2 (Web Development) in Edition 1
--    - Can see team: Web Warriors
--    - Cannot see other tracks or editions
-- 
-- 6. Track Supervisor 3 (track3@supervisor.com / password123)
--    - Can ONLY see Track 4 (Blockchain) in Edition 2
--    - Can see teams: Crypto Coders, Chain Builders
--    - Cannot see other tracks or editions
--