<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die;

/**
 * VirtualCurrency Html Helper
 *
 * @package        VirtualCurrency
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlVirtualcurrency
{
    /**
     * Display an input field for amount
     *
     * @param Virtualcurrency\Currency\Currency $currency
     * @param array $options
     *
     * @return string
     */
    public static function inputAmount($currency, $options)
    {
        $class = '';

        $html = '<div class="input-group">';

        if ($currency->getSymbol()) {
            $html .= '<span class="input-group-addon">' . $currency->getSymbol() . '</span>';
        }

        $id = '';
        if (Joomla\Utilities\ArrayHelper::getValue($options, 'id')) {
            $id = 'id="' . Joomla\Utilities\ArrayHelper::getValue($options, 'id') . '"';
        }

        if (Joomla\Utilities\ArrayHelper::getValue($options, 'class')) {
            $class = 'class="' . Joomla\Utilities\ArrayHelper::getValue($options, 'class') . '"';
        }

        $name = Joomla\Utilities\ArrayHelper::getValue($options, 'name');
        $html .= '<input type="text" name="' . $name . '" value="' . $currency->getParam('minimum') . '" ' . $id . ' ' . $class . ' />';

        if ($currency->getCode()) {
            $html .= '<span class="input-group-addon">' . $currency->getCode() . '</span>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Display the title of the unit.
     *
     * @param Virtualcurrency\Currency\Currency|Virtualcurrency\Commodity\Commodity $unit
     * @param Joomla\Registry\Registry $params
     * @param string $imageFolder
     *
     * @return string
     */
    public static function displayTitle($unit, $params, $imageFolder)
    {
        $html = '';

        if ($params->get('payments_show_icons') and $unit->getIcon()) {
            $html .= '<img src="'.$imageFolder .'/'. $unit->getIcon() .'" /> ';
        }

        $html .= htmlspecialchars($unit->getTitle(), ENT_COMPAT, 'UTF-8');

        return $html;
    }

    /**
     * Calculate total price of unites.
     *
     * @param float $units Number of units.
     * @param float $value   Value for one unit.
     *
     * @return float
     */
    public static function total($units, $value)
    {
        $amount = 0;
        if ($value > 0) {
            $amount = $units * $value;
        }

        return $amount;
    }

    /**
     * Displays price per units.
     *
     * @param stdClass $item
     *
     * @return string
     */
    public static function currencyDetails($item)
    {
        $params = new Registry;
        $params->loadString($item->params);

        $output = array();

        if ($item->code and $item->symbol) {
            $output[] = $item->code . ' ( ' . $item->symbol . ' )';
        } elseif ($item->code and !$item->symbol) {
            $output[] = $item->code;
        } elseif ($item->symbol and !$item->code) {
            $output[] = $item->symbol;
        }

        return implode('<br />', $output);
    }

    /**
     * Displays price per units.
     *
     * @param stdClass $item
     * @param Prism\Money\Money $money
     * @param Virtualcurrency\Currency\RealCurrency $realCurrency
     * @param Virtualcurrency\Currency\Currencies $virtualCurrencies
     *
     * @return string
     */
    public static function price($item, $money, $realCurrency, $virtualCurrencies)
    {
        $output = array();

        if ($item->params->get('price_real')) {
            $money->setCurrency($realCurrency);
            $money->setAmount($item->params->get('price_real'));

            $formattedAmount = $money->formatCurrency();

            if ($item->params->get('minimum')) {
                $formattedAmount .= ' ( ' .JText::sprintf('COM_VIRTUALCURRENCY_MINIMUM_D', $item->params->get('minimum')) . ' )';
            }

            $output[] = $formattedAmount;
        }

        if ($item->params->get('price_virtual') and (int)$item->params->get('currency_id') > 0) {
            $currency  = $virtualCurrencies->getCurrency($item->params->get('currency_id'));

            $money->setCurrency($currency);
            $money->setAmount($item->params->get('price_virtual'));

            $formattedAmount = $money->formatCurrency();

            if ($item->params->get('minimum')) {
                $formattedAmount .= ' ( ' .JText::sprintf('COM_VIRTUALCURRENCY_MINIMUM_D', $item->params->get('minimum')) . ' )';
            }

            $output[] = $formattedAmount;
        }

        return implode('<br />', $output);
    }
}
