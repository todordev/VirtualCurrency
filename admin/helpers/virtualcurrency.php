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

        /*JSubMenuHelper::addEntry(
            JText::_('COM_VIRTUALCURRENCY_PARTNERS'),
            'index.php?option='.self::$extension.'&view=partners',
            $vName == 'partners'
        );*/

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

    /**
     * Create and return money formatter/parser.
     *
     * @return NumberFormatter
     */
    public static function getMoneyFormatter()
    {
        $params          = JComponentHelper::getParams(self::$extension);
        $fractionDigits  = (int)$params->get('fraction_digits');

        $pattern         = '#,##0';
        if ($fractionDigits > 0) {
            $pattern .= '.' .str_repeat('0', $params->get('fraction_digits'));
        }

        $language        = JFactory::getLanguage();
        $moneyFormatter  = new NumberFormatter($language->getTag(), NumberFormatter::PATTERN_DECIMAL, $pattern);

        return $moneyFormatter;
    }

    /**
     * Create and return number formatter/parser.
     *
     * @return NumberFormatter
     */
    public static function getNumberFormatter()
    {
        $params          = JComponentHelper::getParams(self::$extension);
        $fractionDigits  = (int)$params->get('fraction_digits');

        $pattern         = '#0';
        if ($fractionDigits > 0) {
            $pattern .= '.' .str_repeat('0', $params->get('fraction_digits'));
        }

        $language        = JFactory::getLanguage();
        $moneyFormatter  = new NumberFormatter($language->getTag(), NumberFormatter::PATTERN_DECIMAL, $pattern);

        return $moneyFormatter;
    }

    /**
     * This method creates user accounts for every virtual currency.
     *
     * @param  int $userId
     *
     * @return boolean
     */
    public static function createAccounts($userId = 0)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query
            ->select('a.id, c.id as currency_id')
            ->from($db->quoteName('#__users', 'a'))
            ->join('', $db->quoteName('#__vc_currencies', 'c'))
            ->leftJoin($db->quoteName('#__vc_accounts', 'b') . ' ON (a.id = b.user_id AND c.id = b.currency_id)');

        // Filter by user ID.
        if ($userId > 0) {
            $query->where('a.id = ' . (int)$userId);
        }

        $query->where('b.user_id IS NULL');

        $db->setQuery($query);

        $results = $db->loadAssocList();

        if (count($results) > 0) {
            foreach ($results as $result) {
                $account = new Virtualcurrency\Account\Account(JFactory::getDbo());

                $data = array(
                    'user_id' => $result['id'],
                    'currency_id' => $result['currency_id']
                );

                $account->open($data, true);
            }
        }
    }

    /**
     * This method creates commodity records for every user.
     *
     * @param  int $userId
     *
     * @return boolean
     */
    public static function createCommodities($userId = 0)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query
            ->select('a.id, c.id as commodity_id')
            ->from($db->quoteName('#__users', 'a'))
            ->join('', $db->quoteName('#__vc_commodities', 'c'))
            ->leftJoin($db->quoteName('#__vc_usercommodities', 'b') . ' ON (a.id = b.user_id AND c.id = b.commodity_id)');

        // Filter by user ID.
        if ($userId > 0) {
            $query->where('a.id = ' . (int)$userId);
        }

        $query->where('b.user_id IS NULL');

        $db->setQuery($query);

        $results = $db->loadAssocList();

        if (count($results) > 0) {
            foreach ($results as $result) {
                $account = new Virtualcurrency\User\Commodity(JFactory::getDbo());

                $data = array(
                    'user_id' => $result['id'],
                    'commodity_id' => $result['commodity_id']
                );
                $account->bind($data);
                $account->store();
            }
        }
    }

    /**
     * @param stdClass $cartSession
     * @param Joomla\Registry\Registry $params
     *
     * @return null|stdClass
     */
    public static function prepareItem($cartSession, $params)
    {
        $moneyFormatter  = VirtualcurrencyHelper::getMoneyFormatter();
        $money           = new Prism\Money\Money($moneyFormatter);

        if (strcmp('currency', $cartSession->item_type) === 0) {
            $unit       = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
            $unit->load($cartSession->item_id);

            if (!$unit->getId()) {
                return null;
            }

            $itemPrice        = (float)$unit->getParam('price_real');
            $itemPriceVirtual = (float)$unit->getParam('price_virtual');
            $currencyId       = (int)$unit->getParam('currency_id');

            $money->setCurrency($unit);
            $amountFormatted = $money->setAmount($cartSession->items_number)->formatCurrency();
        } else {
            $unit = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
            $unit->load($cartSession->item_id);

            if (!$unit->getId()) {
                return null;
            }

            $itemPrice        = (float)$unit->getParam('price_real');
            $itemPriceVirtual = (float)$unit->getParam('price_virtual');
            $currencyId       = (int)$unit->getParam('currency_id');

            $amountFormatted  = $cartSession->items_number . ' ('.$unit->getTitle().')';
        }

        $realCurrency               = null;
        $totalCostFormatted         = '';
        $totalCostVirtualFormatted  = '';
        $currencyType               = '';

        $order = array(
            'item_type'              => $cartSession->item_type,
            'items_number'           => $cartSession->items_number,
            'items_number_formatted' => 0,
            'real' => array(
                'item_price' => 0,
                'total_cost' => 0,
                'currency_code' => '',
                'items_cost_formatted' => 0,
            ),
            'virtual' => array(
                'item_price' => 0,
                'total_cost' => 0,
                'currency_id' => 0,
                'currency_code' => '',
                'items_cost_formatted' => 0,
            )
        );

        // Get real currency
        if ($itemPrice) {
            $realCurrency = new Virtualcurrency\Currency\RealCurrency(JFactory::getDbo());
            $realCurrency->load($params->get('currency_id'));

            // Calculate total amount that should be paid.
            $totalCost = (string)Prism\Utilities\MathHelper::calculateTotal(array(
                $cartSession->items_number,
                $itemPrice
            ));

            $order['real']['item_price']    = $itemPrice;
            $order['real']['total_cost']    = $totalCost;
            $order['real']['currency_code'] = $realCurrency->getCode();

            $money->setCurrency($realCurrency);
            $totalCostFormatted = $money->setAmount($totalCost)->formatCurrency();

            $currencyType = 'real';
        }

        // Get virtual currency
        if ($itemPriceVirtual) {
            $realCurrency = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
            $realCurrency->load($currencyId);

            // Calculate total amount that should be paid.
            $totalCostVirtual = (string)Prism\Utilities\MathHelper::calculateTotal(array(
                $cartSession->items_number,
                $itemPriceVirtual
            ));

            $order['virtual']['item_price']    = $itemPriceVirtual;
            $order['virtual']['total_cost']    = $totalCostVirtual;
            $order['virtual']['currency_id']   = $realCurrency->getId();
            $order['virtual']['currency_code'] = $realCurrency->getCode();

            $money->setCurrency($realCurrency);
            $totalCostVirtualFormatted = $money->setAmount($totalCostVirtual)->formatCurrency();

            $currencyType = 'virtual';
        }

        $order['items_number_formatted'] = $amountFormatted;
        $order['real']['items_cost_formatted'] = $totalCostFormatted ;
        $order['virtual']['items_cost_formatted'] = $totalCostVirtualFormatted ;

        // Check for possibility to buy virtual goods by real and virtual currencies.
        if ($itemPrice and $itemPriceVirtual) {
            $order['currency_type']  = 'both';
        } else {
            $order['currency_type']  = $currencyType;
        }

        $item = $unit->getProperties();
        $item = Joomla\Utilities\ArrayHelper::toObject($item);

        $item->order = $order;

        return $item;
    }
}
