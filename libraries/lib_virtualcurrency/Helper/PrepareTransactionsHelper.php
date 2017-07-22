<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Helper
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Helper;

use Prism\Domain\BindException;
use Prism\Helper\HelperInterface;
use Prism\Money;
use Virtualcurrency\Currency\Currencies;
use Virtualcurrency\Currency\Currency;
use Virtualcurrency\RealCurrency\Currencies as RealCurrencies;

/**
 * This class provides functionality to prepare transaction data.
 *
 * @package      Virtualcurrency
 * @subpackage   Helper
 */
class PrepareTransactionsHelper implements HelperInterface
{
    protected $formatter;
    protected $currencies;
    protected $realCurrencies;

    /**
     * PrepareTransactionsHelper constructor.
     *
     * @param Money\Formatter $formatter
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
     *
     * @throws BindException
     */
    public function handle(&$data, array $options = array())
    {
        if (count($data) > 0) {
            $foundCurrencies = array();

            foreach ($data as $key => $item) {
                // Format transaction currency.
                if (!array_key_exists($item->txn_currency, $foundCurrencies)) {
                    $currency = $this->realCurrencies->fetchByCode($item->txn_currency);

                    if ($currency === null) {
                        $currency = $this->currencies->fetchByCode($item->txn_currency);
                    }

                    if (!$currency) {
                        $currency = new Currency();
                        $currency->setCode($item->txn_currency);
                    }

                    $foundCurrencies[$item->txn_currency] = $currency;
                } else {
                    $currency = $foundCurrencies[$item->txn_currency];
                }

                $formatCurrency   = new Money\Currency;
                $formatCurrency->bind($currency->getProperties());

                // Format transaction amount.
                $money            = new Money\Money($item->txn_amount, $formatCurrency);
                $item->txn_amount = $this->formatter->formatCurrency($money);

                // Format units.
                $money            = new Money\Money($item->units, $formatCurrency);
                $item->units      = $this->formatter->format($money);
            }
        }
    }
}
