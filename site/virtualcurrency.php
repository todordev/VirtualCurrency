<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Virtualcurrency.init');

$controller = JControllerLegacy::getInstance('VirtualCurrency');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
