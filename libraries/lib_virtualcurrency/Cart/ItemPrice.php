<?php
/**
 * @package         Virtualcurrency/Cart
 * @subpackage      Helper
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Cart;

/**
 * Cart session class.
 *
 * @package         Virtualcurrency/Cart
 * @subpackage      Helper
 */
class ItemPrice
{
    /**
     * @var float
     */
    protected $price = 0.00;

    /**
     * @var float
     */
    protected $total = 0.00;

    /**
     * @var int
     */
    protected $currency_id = 0;

    /**
     * @var string
     */
    protected $currency_code = '';

    /**
     * @var string
     */
    protected $total_formatted = '0.00';

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * @param int $currencyId
     */
    public function setCurrencyId($currencyId)
    {
        $this->currency_id = $currencyId;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currency_code = $currencyCode;
    }

    /**
     * @return string
     */
    public function getTotalFormatted()
    {
        return $this->total_formatted;
    }

    /**
     * @param string $totalFormatted
     */
    public function setTotalFormatted($totalFormatted)
    {
        $this->total_formatted = $totalFormatted;
    }
}
