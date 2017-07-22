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
class Item
{
    protected $item_type = '';
    protected $items_number = 0.00;
    protected $items_number_formatted = '0.00';
    protected $currency_type = '';

    /**
     * @var ItemPrice  Price in real currency.
     */
    protected $real;

    /**
     * @var ItemPrice Price in virtual currency.
     */
    protected $virtual;

    public function __construct($itemType, $itemsNumber)
    {
        $this->item_type    = $itemType;
        $this->items_number = $itemsNumber;
        $this->real         = new ItemPrice;
        $this->virtual      = new ItemPrice;
    }

    /**
     * @return string
     */
    public function getItemType()
    {
        return $this->item_type;
    }

    /**
     * @param string $itemType
     */
    public function setItemType($itemType)
    {
        $this->item_type = $itemType;
    }

    /**
     * @return float
     */
    public function getItemsNumber()
    {
        return $this->items_number;
    }

    /**
     * @param float $itemsNumber
     */
    public function setItemsNumber($itemsNumber)
    {
        $this->items_number = $itemsNumber;
    }

    /**
     * @return string
     */
    public function getItemsNumberFormatted()
    {
        return $this->items_number_formatted;
    }

    /**
     * @param string $itemsNumberFormatted
     */
    public function setItemsNumberFormatted($itemsNumberFormatted)
    {
        $this->items_number_formatted = $itemsNumberFormatted;
    }

    /**
     * @return ItemPrice
     */
    public function getReal()
    {
        return $this->real;
    }

    /**
     * @param ItemPrice $real
     */
    public function setReal(ItemPrice $real)
    {
        $this->real = $real;
    }

    /**
     * @return ItemPrice
     */
    public function getVirtual()
    {
        return $this->virtual;
    }

    /**
     * @param ItemPrice $virtual
     */
    public function setVirtual(ItemPrice $virtual)
    {
        $this->virtual = $virtual;
    }

    /**
     * @return string
     */
    public function getCurrencyType()
    {
        return $this->currency_type;
    }

    /**
     * @param string $currencyType
     */
    public function setCurrencyType($currencyType)
    {
        $this->currency_type = $currencyType;
    }

    /**
     * @param $type
     *
     * @return ItemPrice
     *
     * @throws \InvalidArgumentException
     */
    public function price($type)
    {
        switch ($type) {
            case 'real':
                return $this->real;
                break;

            case 'virtual':
                return $this->virtual;
                break;

            default:
                throw new \InvalidArgumentException('There is no such price type. There are two price types - real and virtual.');
                break;
        }
    }
}
