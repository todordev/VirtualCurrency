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

class VirtualcurrencyModelPayments extends JModelLegacy
{
    /**
     * @param stdClass $cartSession
     * @param Joomla\Registry\Registry $params
     *
     * @return stdClass
     * @throws UnexpectedValueException
     */
    public function prepareItem($cartSession, $params)
    {
        $item = VirtualCurrencyHelper::prepareItem($cartSession, $params);

        if ($item === null or !$item->id) {
            throw new UnexpectedValueException(JText::_('COM_VIRTUALCURRENCY_ERROR_INVALID_ITEM'));
        }

        return $item;
    }
}
