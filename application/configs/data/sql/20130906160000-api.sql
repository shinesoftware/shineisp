INSERT INTO `admin_resources` (`resource_id`, `name`, `hidden`, `is_container`, `is_allowed`, `admin`, `module`, `controller`, `parent_id`) VALUES (NULL, 'API Products', '1', '0', '0', '1', 'api', 'products', '0');
INSERT INTO `admin_permissions` (`permission_id`, `resource_id`, `role_id`, `permission`) VALUES (NULL, LAST_INSERT_ID(), '4', 'allow');

INSERT INTO `admin_resources` (`resource_id`, `name`, `hidden`, `is_container`, `is_allowed`, `admin`, `module`, `controller`, `parent_i`) VALUES (NULL, 'API Orders', '1', '0', '0', '1', 'api', 'orders', '0');
INSERT INTO `admin_permissions` (`permission_id`, `resource_id`, `role_id`, `permission`) VALUES (NULL, LAST_INSERT_ID(), '4', 'allow');

