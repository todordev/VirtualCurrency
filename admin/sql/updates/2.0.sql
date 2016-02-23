ALTER TABLE `#__vc_realcurrencies` CHANGE `abbr` `code` CHAR(3) CHARACTER SET utf8 NOT NULL;

ALTER TABLE `#__vc_currencies` CHANGE `code` `code` VARCHAR(8) CHARACTER SET utf8 NOT NULL;
ALTER TABLE `#__vc_currencies` ADD `position` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `symbol`;
ALTER TABLE `#__vc_currencies` ADD `image` VARCHAR(24) NULL DEFAULT NULL AFTER `symbol`, ADD `image_icon` VARCHAR(24) NULL DEFAULT NULL AFTER `image`;
ALTER TABLE `#__vc_currencies` CHANGE `published` `published` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `#__vc_transactions` ADD `title` VARCHAR(64) NOT NULL AFTER `id`;
ALTER TABLE `#__vc_transactions` CHANGE `currency_id` `item_id` TINYINT(3) UNSIGNED NOT NULL;
ALTER TABLE `#__vc_transactions` ADD `item_type` ENUM('currency','commodity') NOT NULL AFTER `item_id`;
ALTER TABLE `#__vc_transactions` CHANGE `txn_currency` `txn_currency` VARCHAR(8) CHARACTER SET utf8 NOT NULL COMMENT 'A currency of payment by payment gateway.';
ALTER TABLE `#__vc_transactions` ADD `service_alias` VARCHAR(32) NOT NULL AFTER `service_provider`;
ALTER TABLE `#__vc_transactions` ADD `extra_data` VARCHAR(2048) NULL DEFAULT NULL COMMENT 'Additional information about transaction' AFTER `txn_date`;

ALTER TABLE `#__vc_paymentsessions` CHANGE `currency_id` `item_id` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__vc_paymentsessions` CHANGE `amount` `items_number` DECIMAL(10,2) UNSIGNED NOT NULL;
ALTER TABLE `#__vc_paymentsessions` ADD `item_type` ENUM('currency','commodity') NOT NULL DEFAULT 'currency' AFTER `item_id`, ADD `unique_key` VARCHAR(32) NOT NULL DEFAULT '' AFTER `item_type`, ADD `gateway` VARCHAR(32) NOT NULL DEFAULT '' AFTER `unique_key`, ADD `gateway_data` VARCHAR(2048) NULL DEFAULT NULL AFTER `gateway`, ADD `session_id` VARCHAR(32) NOT NULL DEFAULT '' AFTER `gateway_data`;

CREATE TABLE IF NOT EXISTS `#__vc_commodities` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT 'Price in real currency.',
  `price_virtual` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT 'Price in virtual currency.',
  `currency_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `minimum` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `image` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_icon` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__vc_usercommodities` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `commodity_id` tinyint(8) UNSIGNED NOT NULL,
  `number` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;