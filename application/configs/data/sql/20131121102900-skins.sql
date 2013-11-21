UPDATE `settings_parameters` SET `type` = 'select', `config` = '{"class":"Settings","method":"getfrontendskins"}' WHERE `settings_parameters`.`var` = 'skin';
UPDATE `settings_parameters` SET `type` = 'select', `config` = '{"class":"Settings","method":"getbackendskins"}' WHERE `settings_parameters`.`var` = 'adminskin';

INSERT INTO `settings_parameters` (`parameter_id`, `name`, `var`, `type`, `module`, `enabled`, `description`, `group_id`, `config`) VALUES (NULL, 'Custom Frontend CSS', 'css', 'textarea', 'admin', '1', 'Overwrite here the css of the frontend side of ShineISP', '2', NULL);