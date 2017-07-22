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
use Virtualcurrency\Currency\Currencies;
use Virtualcurrency\Currency\Currency;
use Prism\Money;

/**
 * This class provides functionality to prepare accounts data.
 *
 * @package      Virtualcurrency
 * @subpackage   Helper
 */
class PrepareAccountsHelper implements HelperInterface
{
    protected $formatter;
    protected $currencies;

    /**
     * Initialize the object.
     *
     * @param Money\Formatter $formatter
     * @param Currencies $currencies
     */
    public function __construct($formatter, $currencies)
    {
        $this->formatter  = $formatter;
        $this->currencies = $currencies;
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
                // Format account amount.
                if (!array_key_exists($item->currency_id, $foundCurrencies)) {
                    $currency = $this->currencies->fetchById($item->currency_id);
                    if (!$currency) {
                        $currency = new Currency;
                    }

                    $foundCurrencies[$item->currency_id] = $currency;
                } else {
                    $currency = $foundCurrencies[$item->currency_id];
                }

                $formatCurrency   = new Money\Currency;
                $formatCurrency->bind($currency->getProperties());
                $money            = new Money\Money($item->amount, $formatCurrency);

                $item->amount = $this->formatter->formatCurrency($money);
            }
        }
    }
}
