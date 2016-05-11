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
 * Virtualcurrency real currencies controller
 *
 * @package      Virtualcurrency
 * @subpackage   Components
 */
class VirtualcurrencyControllerRealCurrencies extends Prism\Controller\Admin
{
    public function getModel($name = 'RealCurrency', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
