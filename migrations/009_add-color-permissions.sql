-- Migration: 009_add_color_permissions.sql

-- Grant to all super admins
INSERT INTO user_permissions (user_id, module, actions)
SELECT id, 'color_management', '["view", "create", "edit", "delete"]'
FROM users 
WHERE role = 'super_admin' AND deleted_at IS NULL
ON DUPLICATE KEY UPDATE actions = '["view", "create", "edit", "delete"]';

-- Grant to stock managers
INSERT INTO user_permissions (user_id, module, actions)
SELECT id, 'color_management', '["view", "create", "edit"]'
FROM users 
WHERE role = 'stock_manager' AND deleted_at IS NULL
ON DUPLICATE KEY UPDATE actions = '["view", "create", "edit"]';