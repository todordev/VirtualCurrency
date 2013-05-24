<?php
/**
 * @package      Virtual Currency
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Virtual Currency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( "_JEXEC" ) or die;

$userId   = JFactory::getUser()->id;

$accounts = null;

if(!empty($userId)) {
    
    if(!defined("VIRTUALCURRENCY_PATH_COMPONENT_SITE")) {
        define("VIRTUALCURRENCY_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_virtualcurrency");
    }
    
    // Load Virtual Currency HTML helper
    JHtml::addIncludePath(VIRTUALCURRENCY_PATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'html');
    
    jimport("virtualcurrency.accounts");
    
    $db       = JFactory::getDbo();
    
    $vcAccounts = new VirtualCurrencyAccounts($db);
    $vcAccounts->load($userId);
    
    $accounts = $vcAccounts->getAccounts();
    
}

require JModuleHelper::getLayoutPath('mod_virtualcurrencyaccounts', $params->get('layout', 'default'));