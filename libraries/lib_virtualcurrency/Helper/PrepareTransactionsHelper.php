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
use Virtualcurrency\Currency\RealCurrencies;
use Prism\Money\Money;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality to prepare transaction data.
 *
 * @package      Virtualcurrency
 * @subpackage   Helpers
 */
class PrepareTransactionsHelper implements HelperInterface
{
    protected $formatter;
    protected $currencies;
    protected $realCurrencies;

    /**
     * PrepareTransactionsHelper constructor.
     *
     * @param Money $formatter
     * @param Currencies $currencies
     * @param RealCurrencies $realCurrencies
     */
    public function __construct($formatter, $currencies, $realCurrencies)
    {
        $this->formatter = $formatter;
        $this->currencies = $currencies;
        $this->realCurrencies = $realCurrencies;
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
                // Format transaction currency.
                if (!array_key_exists($item->txn_currency, $foundCurrencies)) {
                    $currency = $this->realCurrencies->getCurrency($item->txn_currency);
                    if ($currency === null) {
                        $currency = $this->currencies->getCurrency($item->txn_currency);
                    }

                    if (!$currency) {
                        $currency = new Currency();
                        $currency->setCode($item->txn_currency);
                    }

                    $foundCurrencies[$item->txn_currency] = $currency;
                }

                $currency = $foundCurrencies[$item->txn_currency];

                $this->formatter->setCurrency($currency);
                $item->txn_amount = $this->formatter->setAmount($item->txn_amount)->formatCurrency();

                // Format units.
                $item->units = $this->formatter->setAmount($item->units)->format();
            }
        }
    }
}
