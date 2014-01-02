UPDATE status_history, orders SET status_history.order_id = orders.order_id WHERE status_history.section_id = orders.order_id;
DELETE from status_history where section_id is not null and order_id is null;
ALTER TABLE `status_history` DROP `section_id`;