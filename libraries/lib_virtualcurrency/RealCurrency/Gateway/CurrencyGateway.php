<?php
/**
 * @package         Virtualcurrency\RealCurrency
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\RealCurrency\Gateway;

use Prism\Domain\RichFetcher;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency\RealCurrency
 * @subpackage      Gateway
 */
interface CurrencyGateway extends RichFetcher
{
    /**
     * Return all items as collection.
     *
     * @return array
     */
    public function fetchAll();
}
