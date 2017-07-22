ALTER TABLE `#__vc_commodities` CHANGE `in_stock` `in_stock` SMALLINT(5) NOT NULL DEFAULT '-1';
ALTER TABLE `#__vc_transactions` CHANGE `txn_id` `txn_id` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE `#__vc_paymentsessions` DROP `unique_key`;
ALTER TABLE `#__vc_paymentsessions` DROP `order_id`;
ALTER TABLE `#__vc_paymentsessions` DROP `gateway_data`;

ALTER TABLE `#__vc_paymentsessions` CHANGE `session_id` `session_id` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__vc_transactions` ADD `error_msg` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER `item_type`;

CREATE TABLE IF NOT EXISTS `#__vc_paymentsessiongateways` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Payment session primary key',
  `alias` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Payment service name (alias)',
  `data` varchar(2048) COLLATE utf8_unicode_ci NOT NULL DEFAULT '{}' COMMENT 'Contains a specific data for the gateways',
  `token` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'It is a unique key (token) from the gateway',
  `order_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`alias`),
  UNIQUE KEY `idx_vc_pstoken` (`token`),
  KEY `idx_vc_pspk` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;