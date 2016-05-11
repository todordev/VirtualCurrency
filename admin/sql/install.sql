CREATE TABLE IF NOT EXISTS `#__vc_accounts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `note` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `user_id` int(10) UNSIGNED NOT NULL,
  `currency_id` tinyint(4) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_vc_accounts_uid_cid` (`user_id`,`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__vc_commodities` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `in_stock` smallint(5) UNSIGNED DEFAULT NULL,
  `image` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_icon` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `params` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '{}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__vc_currencies` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `symbol` char(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_icon` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `params` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__vc_partners` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `service_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `note` varchar(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__vc_paymentsessions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `item_type` enum('currency','commodity') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'currency',
  `items_number` decimal(10,2) UNSIGNED NOT NULL,
  `unique_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `order_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gateway` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gateway_data` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `session_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__vc_realcurrencies` (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `symbol` char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `position` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_vc_ccode` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__vc_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `units` decimal(10,2) UNSIGNED NOT NULL COMMENT 'Number units of virtual currency',
  `txn_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `txn_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Paid amount by payment gateway',
  `txn_currency` varchar(8) COLLATE utf8_unicode_ci NOT NULL COMMENT 'A currency of payment by payment gateway.',
  `txn_status` enum('pending','completed','canceled','refunded','failed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `txn_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `extra_data` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Additional information about transaction.',
  `service_provider` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `service_alias` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL COMMENT 'The man who sends the amount.',
  `receiver_id` int(10) UNSIGNED NOT NULL COMMENT 'The man who receives the amount.',
  `item_id` tinyint(3) UNSIGNED NOT NULL,
  `item_type` enum('currency','commodity') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_txns_txn_id` (`txn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__vc_usercommodities` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `commodity_id` tinyint(8) UNSIGNED NOT NULL,
  `number` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
