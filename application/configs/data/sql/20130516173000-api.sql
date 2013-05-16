INSERT INTO admin_resources SET resource_id = 93, name = 'API LegalForms',hidden = 1,is_container= 0,is_allowed= 0,admin= 1,module='api', controller='legalforms',parent_id='0';
INSERT INTO admin_permissions SET permission_id = 128, resource_id=93, role_id=5, permission='allow';
INSERT INTO admin_resources SET resource_id = 94, name = 'API Countries',hidden = 1,is_container= 0,is_allowed= 0,admin= 1,module='api', controller='countries',parent_id='0';
INSERT INTO admin_permissions SET permission_id = 129, resource_id=94, role_id=5, permission='allow';

INSERT INTO admin_resources SET resource_id = 96, name = 'API Orders',hidden = 1,is_container= 0,is_allowed= 0,admin= 1,module='api', controller='orders',parent_id='0';
INSERT INTO admin_permissions SET permission_id = 131, resource_id=96, role_id=5, permission='allow';

