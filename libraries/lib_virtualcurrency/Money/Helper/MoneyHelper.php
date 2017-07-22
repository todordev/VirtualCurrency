<?php
/**
 * @package         Virtualcurrency
 * @subpackage      Money\Helper
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Money\Helper;

use Prism\Money\Formatter\IntlDecimalFormatter;
use Prism\Money\Parser\IntlDecimalParser;
use Virtualcurrency\RealCurrency\Currency;

/**
 * Abstract class of money helpers.
 *
 * @package         Virtualcurrency
 * @subpackage      Money\Helper
 */
abstract class MoneyHelper
{
    /**
     * @return Currency
     */
    abstract public function getCurrency();

    /**
     * @return IntlDecimalFormatter
     */
    abstract public function getFormatter();

    /**
     * @return IntlDecimalParser
     */
    abstract public function getParser();

    protected function prepareNumberFormatter($locale, $digits = 2)
    {
        // Prepare decimal pattern.
        $fractionDigits = (int)$digits;
        $pattern        = '#,##0';
        if ($fractionDigits > 0) {
            $pattern .= '.' . str_repeat('0', $fractionDigits);
        }

        $numberFormatter  = new \NumberFormatter($locale, \NumberFormatter::PATTERN_DECIMAL, $pattern);

        return $numberFormatter;
    }
}
