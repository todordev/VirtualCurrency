<?php
/**
 * This script defines some constants that points to the extension folders.
 *
 * @package      Virtualcurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

if (!defined('VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR')) {
    define('VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtualcurrency');
}

if (!defined('VIRTUALCURRENCY_PATH_COMPONENT_SITE')) {
    define('VIRTUALCURRENCY_PATH_COMPONENT_SITE', JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtualcurrency');
}

if (!defined('VIRTUALCURRENCY_PATH_LIBRARY')) {
    define('VIRTUALCURRENCY_PATH_LIBRARY', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'Virtualcurrency');
}

JLoader::registerNamespace('Virtualcurrency', JPATH_LIBRARIES);

// Register helpers
JLoader::register('VirtualcurrencyHelper', VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR . '/helpers/virtualcurrency.php');
JLoader::register('VirtualcurrencyHelperRoute', VIRTUALCURRENCY_PATH_COMPONENT_SITE . '/helpers/route.php');

// Register Observers
JLoader::register('VirtualcurrencyObserverCurrency', VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR .'/tables/observers/currency.php');
JObserverMapper::addObserverClassToClass('VirtualcurrencyObserverCurrency', 'VirtualCurrencyTableCurrency', array('typeAlias' => 'com_virtualcurrency.currency'));

JLoader::register('VirtualcurrencyObserverCommodity', VIRTUALCURRENCY_PATH_COMPONENT_ADMINISTRATOR .'/tables/observers/commodity.php');
JObserverMapper::addObserverClassToClass('VirtualcurrencyObserverCommodity', 'VirtualCurrencyTableCommodity', array('typeAlias' => 'com_virtualcurrency.commodity'));

// Include HTML helpers path
JHtml::addIncludePath(VIRTUALCURRENCY_PATH_COMPONENT_SITE . '/helpers/html');
