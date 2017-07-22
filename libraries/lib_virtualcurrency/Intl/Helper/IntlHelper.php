<?php
/**
 * @package         Virtualcurrency\Intl
 * @subpackage      Helper
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Intl\Helper;

use Prism\Intl\Formatter\IntlDecimalFormatter;
use Prism\Intl\Parser\IntlDecimalParser;

/**
 * Abstract class of Intl helpers.
 *
 * @package         Virtualcurrency\Intl
 * @subpackage      Helper
 */
abstract class IntlHelper
{
    /**
     * @return IntlDecimalFormatter
     */
    abstract public function getNumberFormatter();

    /**
     * @return IntlDecimalParser
     */
    abstract public function getNumberParser();

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
