<?php
/**
 * This script defines some constants that points to the extension folders.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

if (!defined("VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR")) {
    define("VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_virtualcurrency");
}

if (!defined("VIRTUALCURRENCY_PATH_COMPONENT_SITE")) {
    define("VIRTUALCURRENCY_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_virtualcurrency");
}

if (!defined("VIRTUALCURRENCY_PATH_LIBRARY")) {
    define("VIRTUALCURRENCY_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "virtualcurrency");
}

jimport('joomla.utilities.arrayhelper');

// Register constants
JLoader::register("VirtualCurrencyConstants", VIRTUALCURRENCY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "constants.php");

// Register classes and helpers
JLoader::register("VirtualCurrencyHelper", VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "virtualcurrency.php");
JLoader::register("VirtualCurrencyVersion", VIRTUALCURRENCY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "version.php");
JLoader::register("VirtualCurrencyHelperRoute", VIRTUALCURRENCY_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");

// Register some most used classes
JLoader::register("VirtualCurrencyCurrency", VIRTUALCURRENCY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "currency.php");
JLoader::register("VirtualCurrencyCurrencies", VIRTUALCURRENCY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "currencies.php");
JLoader::register("VirtualCurrencyAccount", VIRTUALCURRENCY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "account.php");
JLoader::register("VirtualCurrencyAccounts", VIRTUALCURRENCY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "accounts.php");
JLoader::register("VirtualCurrencyPaymentSession", VIRTUALCURRENCY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "payment" . DIRECTORY_SEPARATOR . "session.php");

// Prepare logger
$registry = JRegistry::getInstance("com_virtualcurrency");
/** @var  $registry Joomla\Registry\Registry */

//$registry->set("logger.table", "#__vc_logs");
$registry->set("logger.file", "com_virtualcurrency.php");

// Include HTML helpers path
JHtml::addIncludePath(VIRTUALCURRENCY_PATH_COMPONENT_SITE . '/helpers/html');