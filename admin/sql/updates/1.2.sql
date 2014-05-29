ALTER TABLE `#__vc_currencies` ADD `description` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `title` ;
ALTER TABLE `#__vc_currencies` DROP `amount`, DROP `currency`, DROP `minimum`;
ALTER TABLE `#__vc_currencies` ADD `params` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `symbol` ;

RENAME TABLE `#__vc_tmp` TO `#__vc_paymentsessions` ;
ALTER TABLE `#__vc_paymentsessions` CHANGE `number` `amount` DECIMAL( 10, 2 ) UNSIGNED NOT NULL ;

ALTER TABLE `#__vc_transactions` CHANGE `number` `units` DECIMAL( 10, 2 ) UNSIGNED NOT NULL COMMENT 'Number units of virtual currency';
ALTER TABLE `#__vc_transactions` CHANGE `txn_status` `txn_status` ENUM( 'pending', 'completed', 'canceled', 'refunded', 'failed' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pending';

ALTER TABLE `#__vc_accounts` CHANGE `amount` `amount` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `#__vc_realcurrencies` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `abbr` char(3) NOT NULL,
  `symbol` char(3) NOT NULL DEFAULT '',
  `position` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_vc_ccode` (`abbr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vc_emails` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;