<?php
/**
 * @package      Virtual Currency
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined("_JEXEC") or die;

jimport('Prism.init');
jimport('Virtualcurrency.init');

$userId = JFactory::getUser()->get('id');

$accounts = null;

if ($userId > 0) {
    $accounts = new Virtualcurrency\Account\Accounts(JFactory::getDbo());
    $accounts->load(array('user_id' => $userId, 'state' => Prism\Constants::PUBLISHED));

    $currencies = new Virtualcurrency\Currency\Currencies(JFactory::getDbo());
    $currencies->load();

    $moneyFormatter  = VirtualcurrencyHelper::getMoneyFormatter();
    $money           = new Prism\Money\Money($moneyFormatter);

    require JModuleHelper::getLayoutPath('mod_virtualcurrencyaccounts', $params->get('layout', 'default'));
}
