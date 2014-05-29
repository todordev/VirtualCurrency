<?php
/**
 * @package      Virtual Currency
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined("_JEXEC") or die;

jimport("itprism.init");
jimport("virtualcurrency.init");

$userId = JFactory::getUser()->get("id");

$accounts = null;

if (!empty($userId)) {
    
    $accounts = new VirtualCurrencyAccounts(JFactory::getDbo());
    $accounts->load($userId);

    $options = array(
        "state" => VirtualCurrencyConstants::PUBLISHED
    );
    
    $currencies = new VirtualCurrencyCurrencies(JFactory::getDbo());
    $currencies->load($options);
}

require JModuleHelper::getLayoutPath('mod_virtualcurrencyaccounts', $params->get('layout', 'default'));