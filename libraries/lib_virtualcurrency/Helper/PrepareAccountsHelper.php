<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Helper;

use Prism\Helper\HelperInterface;
use Virtualcurrency\Currency\Currencies;
use Virtualcurrency\Currency\Currency;
use Prism\Money\Money;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality to prepare accounts data.
 *
 * @package      Virtualcurrency
 * @subpackage   Helpers
 */
class PrepareAccountsHelper implements HelperInterface
{
    protected $formatter;
    protected $currencies;

    /**
     * Initialize the object.
     *
     * @param Money $formatter
     * @param Currencies $currencies
     */
    public function __construct($formatter, $currencies)
    {
        $this->formatter = $formatter;
        $this->currencies = $currencies;
    }

    /**
     * Prepare the parameters of the items.
     *
     * @param array $data
     * @param array $options
     */
    public function handle(&$data, array $options = array())
    {
        if (count($data) > 0) {
            $foundCurrencies = array();
            
            foreach ($data as $key => $item) {
                // Format account amount.
                if (!array_key_exists($item->currency_id, $foundCurrencies)) {
                    $currency = $this->currencies->getCurrency($item->currency_id);
                    if (!$currency) {
                        $currency = new Currency();
                    }

                    $foundCurrencies[$item->currency_id] = $currency;
                }

                $currency = $foundCurrencies[$item->currency_id];

                $this->formatter->setCurrency($currency);
                $item->amount = $this->formatter->setAmount($item->amount)->formatCurrency();
            }
        }
    }
}
