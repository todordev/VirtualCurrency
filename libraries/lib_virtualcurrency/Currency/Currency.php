<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Currency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Currency;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\Populator;
use Joomla\Registry\Registry;
use Prism\Domain\ParamsMethods;
use Prism\Domain\PropertiesMethods;

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Virtualcurrency
 * @subpackage   Currency
 */
class Currency implements Entity
{
    use EntityId, Populator, ParamsMethods, PropertiesMethods;

    protected $title;
    protected $description;
    protected $code;
    protected $symbol;
    protected $position;

    protected $image;
    protected $image_icon;
    protected $published;

    public function __construct()
    {
        $this->params = new Registry;
    }

    /**
     * This method calculates the amount of the units.
     * You have to give the number of your units that you would like to calculate.
     * The method will calculate the price of those units.
     *
     * <code>
     *  $currencyId  = 1;
     *
     *  $currency    = Virtualcurrency\Currency\Currency::getInstance(JFactory::getDbo, $currencyId);
     *
     *  // It is the number of units, that I would like to buy.
     *  $unitsNumber = 10;
     *  $amount      = $currency->calculate($unitsNumber);
     * </code>
     *
     * @param  int $units
     *
     * @return string Amount
     */
    public function calculate($units)
    {
        $amount = 0;
        if ($units > 0) {
            $amount = $this->params->get('price_real');
            $amount *= $units;
        }

        return (string)$amount;
    }

    /**
     * Return the description of the unit (virtual currency).
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $description = $currency->getDescription();
     * </code>
     *
     * @return string
     */
    public function getDescription()
    {
        return (string)$this->description;
    }

    /**
     * Set the code of the unit (virtual currency).
     *
     * <code>
     * $currency    = new Virtualcurrency\Currency\Currency();
     * $currency->setCode('GOLD');
     * </code>
     *
     * @param string $code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Return the code (abbreviation) of the unit (virtual currency).
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $code = $currency->getCode();
     * </code>
     *
     * @return string
     */
    public function getCode()
    {
        return (string)$this->code;
    }

    /**
     * Return the symbol of the unit (virtual currency).
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $symbol = $currency->getSymbol();
     * </code>
     *
     * @return string
     */
    public function getSymbol()
    {
        return (string)$this->symbol;
    }

    /**
     * Set the symbol of the unit (virtual currency).
     *
     * <code>
     * $currency    = new Virtualcurrency\Currency\Currency();
     * $currency->setSymbol('€');
     * </code>
     *
     * @param string $symbol
     *
     * @return self
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Return the title of the virtual currency.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * $title = $currency->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return the position of currency symbol.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Virtualcurrency\Currency\Currency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * // Return 0 = beginning; 1 = end;
     * if (0 == $currency->getPosition()) {
     * ...
     * }
     * </code>
     *
     * @return int
     */
    public function getPosition()
    {
        return (int)$this->position;
    }

    /**
     * Return the image of the currency.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Virtualcurrency\Currency\Currency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * echo $currency->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Return the icon of the currency.
     *
     * <code>
     * $currencyId  = 1;
     *
     * $currency    = new Virtualcurrency\Currency\Currency(\JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * echo $currency->getImage();
     * </code>
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->image_icon;
    }
}
