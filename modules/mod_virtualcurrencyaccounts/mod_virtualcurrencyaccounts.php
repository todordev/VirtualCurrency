<?php
/**
 * @package      Virtual Currency
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Database\Condition\Condition;
use Prism\Database\Condition\Conditions;
use Prism\Database\Request\Request;

// no direct access
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Virtualcurrency.init');

$userId = JFactory::getUser()->get('id');

$accounts = null;

if ($userId > 0) {
    // Prepare conditions.
    $conditionUserId = new Condition(['column' => 'user_id', 'value' => $userId, 'operator'=> '=', 'table' => 'a']);
    $conditionState  = new Condition(['column' => 'published', 'value' => Prism\Constants::PUBLISHED, 'operator'=> '=', 'table' => 'a']);

    $conditions = new Conditions();
    $conditions
        ->addCondition($conditionUserId)
        ->addCondition($conditionState);

    $databaseRequest = new Request();
    $databaseRequest->setConditions($conditions);

    // Fetch results.
    $mapper     = new \Virtualcurrency\Account\Mapper(new \Virtualcurrency\Account\Gateway\JoomlaGateway(JFactory::getDbo()));
    $repository = new \Virtualcurrency\Account\Repository($mapper);
    $accounts   = $repository->fetchCollection($databaseRequest);

    $mapper     = new \Virtualcurrency\Currency\Mapper(new \Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo()));
    $repository = new \Virtualcurrency\Currency\Repository($mapper);
    $currencies = $repository->fetchAll();

    $formatter = \Virtualcurrency\Money\Helper::factory('joomla')->getFormatter();

    require JModuleHelper::getLayoutPath('mod_virtualcurrencyaccounts', $params->get('layout', 'default'));
}
