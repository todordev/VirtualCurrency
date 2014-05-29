<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * VirtualCurrency Html Helper
 *
 * @package        ITPrism Components
 * @subpackage     VirtualCurrency
 * @since          1.6
 */
abstract class JHtmlVirtualCurrency
{
    /**
     * Display an input field for amount
     *
     * @param float $value
     * @param array $currency
     * @param array $options
     *
     * @return string
     */
    public static function inputAmount($value, $currency, $options)
    {
        $class = "";
        if (!empty($currency["symbol"])) {
            $class = "input-prepend ";
        }

        $class .= "input-append";

        $html = '<div class="' . $class . '">';

        if (!empty($currency["symbol"])) {
            $html .= '<span class="add-on">' . $currency["symbol"] . '</span>';
        }

        $name = JArrayHelper::getValue($options, "name");

        $id = "";
        if (JArrayHelper::getValue($options, "id")) {
            $id = 'id="' . JArrayHelper::getValue($options, "id") . '"';
        }

        if (!$value or !is_numeric($value)) {
            $value = 0;
        }

        if (JArrayHelper::getValue($options, "class")) {
            $class = 'class="' . JArrayHelper::getValue($options, "class") . '"';
        }

        $html .= '<input type="text" name="' . $name . '" value="' . $value . '" ' . $id . ' ' . $class . ' />';

        if (!empty($currency["code"])) {
            $html .= '<span class="add-on">' . $currency["code"] . '</span>';
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
        if (!empty($value)) {
            $amount = $units * $value;
        }

        return $amount;
    }
}
