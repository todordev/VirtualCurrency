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
    $mapper     = new \Virtualcurrency\Account\Mapper(new \Virtualcurrency\Account\Gateway\JoomlaGateway(JFactory::getDbo()));
    $repository = new \Virtualcurrency\Account\Repository($mapper);
    $accounts   = $repository->fetchCollection(['user_id' => $userId, 'state' => Prism\Constants::PUBLISHED]);

    $mapper     = new \Virtualcurrency\Currency\Mapper(new \Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo()));
    $repository = new \Virtualcurrency\Currency\Repository($mapper);
    $currencies = $repository->fetchAll();

    $formatter = \Virtualcurrency\Money\Helper::factory('joomla')->getFormatter();

    require JModuleHelper::getLayoutPath('mod_virtualcurrencyaccounts', $params->get('layout', 'default'));
}
