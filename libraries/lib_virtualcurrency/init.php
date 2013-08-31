<?php
/**
 * This script defines some constants that points to the extension folders.
 * 
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

if(!defined("VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR")) {
    define("VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_virtualcurrency");
}

if(!defined("VIRTUALCURRENCY_PATH_COMPONENT_SITE")) {
    define("VIRTUALCURRENCY_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_virtualcurrency");
}

if(!defined("VIRTUALCURRENCY_PATH_LIBRARY")) {
    define("VIRTUALCURRENCY_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "virtualcurrency");
}

if(!defined("ITPRISM_PATH_LIBRARY")) {
    define("ITPRISM_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "itprism");
}

jimport('joomla.utilities.arrayhelper');

// Register classes and helpers
JLoader::register("VirtualCurrencyHelper",  VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "virtualcurrency.php");
JLoader::register("VirtualCurrencyVersion", VIRTUALCURRENCY_PATH_LIBRARY . DIRECTORY_SEPARATOR . "version.php");
JLoader::register("VirtualCurrencyHelperRoute", VIRTUALCURRENCY_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");

// ITPrism classes
JLoader::register("ITPrismErrors", ITPRISM_PATH_LIBRARY . DIRECTORY_SEPARATOR . "errors.php");