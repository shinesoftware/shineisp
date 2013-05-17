INSERT INTO admin_resources SET resource_id = 98, NAME = 'API Regions',hidden = 1,is_container= 0,is_allowed= 0,admin= 1,module='api', controller='regions',parent_id='0';
INSERT INTO admin_permissions SET permission_id = 133, resource_id=98, role_id=5, permission='allow';

DELETE FROM admin_permissions	WHERE	permission_id = 130;
DELETE FROM admin_resources 	WHERE	resource_id = 95;