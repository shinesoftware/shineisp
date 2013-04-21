
INSERT INTO admin_resources SET name = 'Email Templates', hidden = 0, is_container = 0, is_allowed = 0, admin = 1, module = 'admin', controller = 'emailstemplates', parent_id = 60;
INSERT INTO navigation SET label = 'Email Templates', description = 'Manage templates for email sent by ShineISP', uri = '/admin/emailstemplates', parent_id = 12, module = 'admin', controller = 'emailstemplates';  