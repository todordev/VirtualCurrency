<?php
/**
 * @package      Virtual Currency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * It is Virtual Currency helper class
 */
class VirtualCurrencyHelper
{
    protected static $extension = "com_virtualcurrency";

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
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_CURRENCIES'),
            'index.php?option=' . self::$extension . '&view=currencies',
            $vName == 'currencies'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_ACCOUNTS'),
            'index.php?option=' . self::$extension . '&view=accounts',
            $vName == 'accounts'
        );

        /*JSubMenuHelper::addEntry(
            JText::_('COM_VIRTUALCURRENCY_PARTNERS'),
            'index.php?option='.self::$extension.'&view=partners',
            $vName == 'partners'
        );*/

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_TRANSACTIONS'),
            'index.php?option=' . self::$extension . '&view=transactions',
            $vName == 'transactions'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_REAL_CURRENCIES'),
            'index.php?option=' . self::$extension . '&view=realcurrencies',
            $vName == 'realcurrencies'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_EMAILS'),
            'index.php?option=' . self::$extension . '&view=emails',
            $vName == 'emails'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIRTUALCURRENCY_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=' . rawurlencode("virtual currency"),
            $vName == 'plugins'
        );

    }

    /**
     * This method returns account ID using user id and currency id.
     *
     * @param  integer $userId
     * @param  integer $currencyId
     *
     * @return boolean
     */
    public static function getAccountId($userId, $currencyId)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query
            ->select("a.id")
            ->from($db->quoteName("#__vc_accounts", "a"))
            ->where("a.user_id = " . (int)$userId)
            ->where("a.currency_id = " . (int)$currencyId);

        $db->setQuery($query, 0, 1);

        return (bool)$db->loadResult();
    }
}
