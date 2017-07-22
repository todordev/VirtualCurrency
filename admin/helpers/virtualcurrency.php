<?php
/**
 * @package      Virtual Currency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * It is Virtual Currency helper class
 */
class VirtualcurrencyHelper
{
    protected static $extension = 'com_virtualcurrency';

    /**
     * Configure the Linkbar.
     *
     * @param    string  $vName  The name of the active view.
     *
     * @since    1.6
     */
    public static function addSubmenu($vName = 'dashboard')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_DASHBOARD'),
            'index.php?option=' . self::$extension . '&view=dashboard',
            $vName === 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_CURRENCIES'),
            'index.php?option=' . self::$extension . '&view=currencies',
            $vName === 'currencies'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_VIRTUAL_GOODS'),
            'index.php?option=' . self::$extension . '&view=commodities',
            $vName === 'commodities'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_ACCOUNTS'),
            'index.php?option=' . self::$extension . '&view=accounts',
            $vName === 'accounts'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_TRANSACTIONS'),
            'index.php?option=' . self::$extension . '&view=transactions',
            $vName === 'transactions'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_REAL_CURRENCIES'),
            'index.php?option=' . self::$extension . '&view=realcurrencies',
            $vName === 'realcurrencies'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_TOOLS'),
            'index.php?option=' . self::$extension . '&view=tools',
            $vName === 'tools'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=' . rawurlencode('virtual currency'),
            $vName === 'plugins'
        );
    }
}
