<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Virtualcurrency Accounts controller
 *
 * @package     ITPrism Components
 * @subpackage  Virtualcurrency
 */
class VirtualcurrencyControllerAccounts extends Prism\Controller\Admin
{
    public function getModel($name = 'Account', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
