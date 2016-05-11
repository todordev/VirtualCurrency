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
 * Virtualcurrency tools controller class.
 *
 * @package        Virtualcurrency
 * @subpackage     Components
 * @since          1.6
 */
class VirtualcurrencyControllerTools extends Prism\Controller\DefaultController
{
    public function getModel($name = 'Tools', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
