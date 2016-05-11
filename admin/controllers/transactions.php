<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Virtual Currency transactions controller class.
 *
 * @package        Virtualcurrency
 * @subpackage     Components
 * @since          1.6
 */
class VirtualcurrencyControllerTransactions extends Prism\Controller\Admin
{
    public function getModel($name = 'Transaction', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
