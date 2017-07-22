<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Commodity
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Commodity;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\EntityProperties;
use Prism\Domain\Populator;
use Prism\Domain\ParamsMethods;
use Prism\Domain\PropertiesMethods;

/**
 * This class contains methods that are used for managing commodity.
 *
 * @package      Virtualcurrency
 * @subpackage   Commodity
 */
class Commodity implements Entity, EntityProperties
{
    use EntityId, Populator, ParamsMethods, PropertiesMethods;

    protected $title;
    protected $description;
    protected $in_stock;
    protected $image;
    protected $image_icon;
    protected $published;

    /**
     * Return the description of the unit.
     *
     * @return string
     */
    public function getDescription()
    {
        return (string)$this->description;
    }

    /**
     * Return number of the units in stock.
     *
     * @return int
     */
    public function getInStock()
    {
        return (int)$this->in_stock;
    }

    /**
     * Set number of the units in stock.
     *
     * @param int $number
     */
    public function setInStock($number)
    {
        $this->in_stock = (int)$number;
    }

    /**
     * Return the title of the unit.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return the image of the commodity.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Return the icon of the commodity.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->image_icon;
    }

    /**
     * Return number of minimum units that users can buy in an order.
     *
     * @return float
     */
    public function getMinimum()
    {
        return (float)$this->params->get('minimum');
    }

    /**
     * Return the price of the commodity in real currency.
     *
     * @return string
     */
    public function getPriceReal()
    {
        return $this->params->get('price_real');
    }

    /**
     * Return the price of the commodity in virtual currency.
     *
     * @return string
     */
    public function getPriceVirtual()
    {
        return $this->params->get('price_virtual');
    }

    /**
     * Return the ID of the virtual currency in which this commodity can be sold.
     *
     * @return int
     */
    public function getCurrencyId()
    {
        return (int)$this->params->get('currency_id');
    }

    /**
     * Check for enough units.
     *
     * @param int $number
     *
     * @return bool
     */
    public function hasUnits($number = 0)
    {
        $inStock = (int)$this->in_stock;

        // There are unlimited units.
        if ($inStock === -1) {
            return true;
        }

        if ($inStock <= 0) {
            return false;
        }

        $number = (int)abs($number);

        return $number <= $inStock;
    }

    /**
     * Check for unlimited units.
     *
     * @return bool
     */
    public function isUnlimited()
    {
        return ((int)$this->in_stock === -1);
    }

    /**
     * Decrease the number of items in stock.
     *
     * @param  int $number
     */
    public function decreaseInStock($number)
    {
        if ($this->in_stock > 0 and ($this->in_stock - $number) >= 0) {
            $this->in_stock -= (int)$number;
        }
    }

    /**
     * Calculate the price in real currency.
     *
     * @param int $numberOfUnits
     *
     * @return float
     */
    public function calculateRealPrice($numberOfUnits)
    {
        $price = $this->params->get('price_real', 0.00);
        if ($price > 0 and $numberOfUnits > 0) {
            return round($price * $numberOfUnits, 2);
        }

        return 0.00;
    }

    /**
     * Return the price in virtual currency.
     *
     * @param int $numberOfUnits
     *
     * @return float
     */
    public function calculateVirtualPrice($numberOfUnits)
    {
        $price = $this->params->get('price_virtual', 0.00);
        if ($price > 0 and $numberOfUnits > 0) {
            return round($price * $numberOfUnits, 2);
        }

        return 0.00;
    }
}
