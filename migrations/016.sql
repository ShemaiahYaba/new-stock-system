-- Grant tile permissions to Super Admins
INSERT INTO user_permissions (user_id, module, actions)
SELECT id, 'design_management', '["view","create","edit","delete"]'
FROM users 
WHERE role = 'super_admin' AND deleted_at IS NULL
ON DUPLICATE KEY UPDATE actions = '["view","create","edit","delete"]';

INSERT INTO user_permissions (user_id, module, actions)
SELECT id, 'tile_management', '["view","create","edit","delete"]'
FROM users 
WHERE role = 'super_admin' AND deleted_at IS NULL
ON DUPLICATE KEY UPDATE actions = '["view","create","edit","delete"]';

INSERT INTO user_permissions (user_id, module, actions)
SELECT id, 'tile_sales', '["view","create","edit","delete"]'
FROM users 
WHERE role = 'super_admin' AND deleted_at IS NULL
ON DUPLICATE KEY UPDATE actions = '["view","create","edit","delete"]';