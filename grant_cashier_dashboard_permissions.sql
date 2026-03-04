-- SQL script to grant dashboard.data permission to cashier users
-- Run this in your database to ensure cashier users can see dashboard metrics

-- First, check if dashboard.data permission exists
SELECT id, name FROM permissions WHERE name = 'dashboard.data';

-- Grant dashboard.data permission to all cashier roles
-- Replace 81 with the actual permission ID from the query above if different
INSERT IGNORE INTO role_has_permissions (permission_id, role_id) 
SELECT 
    (SELECT id FROM permissions WHERE name = 'dashboard.data' LIMIT 1) as permission_id,
    id as role_id
FROM roles 
WHERE name LIKE 'Cashier#%';

-- Also grant permission directly to users who have sell.create but not dashboard.data
-- This covers users who might not have proper role assignments
INSERT IGNORE INTO model_has_permissions (permission_id, model_type, model_id)
SELECT 
    (SELECT id FROM permissions WHERE name = 'dashboard.data' LIMIT 1) as permission_id,
    'App\\User' as model_type,
    u.id as model_id
FROM users u
WHERE u.id IN (
    -- Users who have sell.create permission
    SELECT mhp.model_id 
    FROM model_has_permissions mhp 
    JOIN permissions p ON p.id = mhp.permission_id 
    WHERE p.name = 'sell.create' 
    AND mhp.model_type = 'App\\User'
)
AND u.id NOT IN (
    -- But don't have dashboard.data permission
    SELECT mhp2.model_id 
    FROM model_has_permissions mhp2 
    JOIN permissions p2 ON p2.id = mhp2.permission_id 
    WHERE p2.name = 'dashboard.data' 
    AND mhp2.model_type = 'App\\User'
)
AND u.id NOT IN (
    -- And are not superadmin users
    SELECT mhp3.model_id 
    FROM model_has_permissions mhp3 
    JOIN permissions p3 ON p3.id = mhp3.permission_id 
    WHERE p3.name = 'superadmin' 
    AND mhp3.model_type = 'App\\User'
);

-- Verify the changes
SELECT 
    u.username,
    u.first_name,
    u.last_name,
    r.name as role_name,
    'dashboard.data' as permission_granted
FROM users u
JOIN model_has_roles mhr ON mhr.model_id = u.id AND mhr.model_type = 'App\\User'
JOIN roles r ON r.id = mhr.role_id
JOIN role_has_permissions rhp ON rhp.role_id = r.id
JOIN permissions p ON p.id = rhp.permission_id
WHERE p.name = 'dashboard.data'
AND r.name LIKE 'Cashier#%'
ORDER BY u.username;