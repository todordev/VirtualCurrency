SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `#__vc_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,0) NOT NULL DEFAULT '0',
  `note` varchar(512) NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vc_currencies` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `code` char(4) NOT NULL,
  `symbol` char(4) NOT NULL DEFAULT '',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `currency` char(4) NOT NULL,
  `minimum` int(10) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vc_partners` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `website` varchar(255) NOT NULL DEFAULT '',
  `service_url` varchar(255) NOT NULL DEFAULT '',
  `note` varchar(512) NOT NULL DEFAULT '',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vc_tmp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `number` decimal(10,2) unsigned NOT NULL,
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vc_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `number` decimal(10,2) unsigned NOT NULL COMMENT 'Number units of virtual currency',
  `txn_id` varchar(255) NOT NULL,
  `txn_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Paid amount by payment gateway',
  `txn_currency` char(4) NOT NULL COMMENT 'A currency of payment by payment gateway.',
  `txn_status` varchar(32) NOT NULL,
  `txn_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `service_provider` varchar(32) NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL COMMENT 'ID of virtual currency',
  `sender_id` int(10) unsigned NOT NULL COMMENT 'The man who sends the amount.',
  `receiver_id` int(10) unsigned NOT NULL COMMENT 'The man who receives the amount.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_txns_txn_id` (`txn_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
