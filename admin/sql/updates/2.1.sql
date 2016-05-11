ALTER TABLE `#__vc_commodities` CHANGE `number` `in_stock` SMALLINT(5) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `#__vc_commodities` DROP `price`, DROP `price_virtual`, DROP `currency_id`, DROP `minimum`;
ALTER TABLE `#__vc_commodities` ADD `params` VARCHAR(255) NOT NULL DEFAULT '{}' AFTER `published`;

ALTER TABLE `#__vc_accounts` ADD `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `note`;
ALTER TABLE `#__vc_accounts` ADD `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `created_at`;

ALTER TABLE `#__vc_paymentsessions` ADD `order_id` VARCHAR(32) NOT NULL DEFAULT '' AFTER `unique_key`;