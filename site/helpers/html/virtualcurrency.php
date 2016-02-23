<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * VirtualCurrency Html Helper
 *
 * @package        VirtualCurrency
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlVirtualCurrency
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
     * @param Virtualcurrency\Amount $amountFormatter
     * @param Virtualcurrency\Currency\Real\Currency $realCurrency
     * @param Virtualcurrency\Currency\Currencies $virtualCurrencies
     *
     * @return string
     */
    public static function currencyPrice($item, $amountFormatter, $realCurrency, $virtualCurrencies)
    {
        $params = new \Joomla\Registry\Registry($item->params);

        $output = array();

        if ($params->get('price')) {
            $amountFormatter->setCurrency($realCurrency);
            $output[] = $amountFormatter->setValue($params->get('price'))->formatCurrency() . ' ( ' .JText::sprintf('COM_VIRTUALCURRENCY_MINIMUM_D', $params->get('minimum')) . ' )';
        }

        if ($params->get('price_virtual') and (int)$params->get('currency_id') > 0) {
            $virtualCurrency = $virtualCurrencies->getCurrency($params->get('currency_id'));

            $amountFormatter->setCurrency($virtualCurrency);
            $output[] = $amountFormatter->setValue($params->get('price_virtual'))->formatCurrency() . ' ( ' .JText::sprintf('COM_VIRTUALCURRENCY_MINIMUM_D', $params->get('minimum')) . ' )';
        }

        return implode('<br />', $output);
    }

    /**
     * Displays price per units.
     *
     * @param stdClass $item
     * @param Virtualcurrency\Amount $amount
     * @param Virtualcurrency\Currency\Real\Currency $realCurrency
     * @param Virtualcurrency\Currency\Currencies $virtualCurrencies
     *
     * @return string
     */
    public static function virtualGoodsPrice($item, $amount, $realCurrency, $virtualCurrencies)
    {
        $output = array();

        if ($item->price) {
            $amount->setCurrency($realCurrency);
            $output[] = $amount->setValue($item->price)->formatCurrency() . ' ( ' .JText::sprintf('COM_VIRTUALCURRENCY_MINIMUM_D', $item->minimum) . ' )';
        }

        if ($item->price_virtual and (int)$item->currency_id > 0) {
            $virtualCurrency = $virtualCurrencies->getCurrency($item->currency_id);

            $amount->setCurrency($virtualCurrency);
            $output[] = $amount->setValue($item->price_virtual)->formatCurrency() . ' ( ' .JText::sprintf('COM_VIRTUALCURRENCY_MINIMUM_D', $item->minimum) . ' )';
        }

        return implode('<br />', $output);
    }
}
