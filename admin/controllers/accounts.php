<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * VirtualCurrency Accounts controller
 *
 * @package     ITPrism Components
 * @subpackage  VirtualCurrency
 */
class VirtualCurrencyControllerAccounts extends Prism\Controller\Admin
{
    public function getModel($name = 'Account', $prefix = 'VirtualCurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
