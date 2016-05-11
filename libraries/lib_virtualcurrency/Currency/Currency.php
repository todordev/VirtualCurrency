<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Currencies
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Currency;

use Prism\Database;
use Prism\Money\CurrencyInterface;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Virtualcurrency
 * @subpackage   Currencies
 */
class Currency extends Database\Table implements CurrencyInterface
{
    protected $id;
    protected $title;
    protected $description;
    protected $code;
    protected $symbol;
    protected $position;
    protected $image;
    protected $image_icon;
    protected $published;

    /**
     * Load currency data from database.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency   = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
     * $currency->load($currencyId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.title, a.symbol, a.code, a.position, a.published, a.image, a.image_icon, a.params')
            ->from($this->db->quoteName('#__vc_currencies', 'a'));

        if (is_array($keys)) {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) .' = ' . $this->db->quote($value));
            }
        } else {
            $query->where('a.id = ' . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * Store the data in database.
     *
     * <code>
     * $data = (
     *  "title"    => "Coins",
     *  "description"  => "My description...",
     *  "code" => "C",
     *  "symbol" => "$"
     * );
     *
     * $currency   = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
     * $currency->bind($data);
     * $currency->store();
     * </code>
     *
     */
    public function store()
    {
        if (!$this->id) { // Insert
            $this->insertObject();
        } else { // Update
            $this->updateObject();
        }
    }

    protected function insertObject()
    {
        $description   = (!$this->description) ? 'NULL' : $this->db->quote($this->description);
        $params        = (!$this->params) ? 'NULL' : $this->db->quote($this->params->toString());

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_currencies'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('description') . '=' . $description)
            ->set($this->db->quoteName('code') . '=' .$this->db->quote($this->code))
            ->set($this->db->quoteName('symbol') . '=' .$this->db->quote($this->symbol))
            ->set($this->db->quoteName('position') . '=' . (int)$this->position)
            ->set($this->db->quoteName('params') . '=' . $params);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $description   = (!$this->description) ? 'NULL' : $this->db->quote($this->description);
        $params        = (!$this->params) ? 'NULL' : $this->db->quote($this->params->toString());

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_currencies'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('description') . '=' . $description)
            ->set($this->db->quoteName('code') . '=' .$this->db->quote($this->code))
            ->set($this->db->quoteName('symbol') . '=' .$this->db->quote($this->symbol))
            ->set($this->db->quoteName('position') . '=' . (int)$this->position)
            ->set($this->db->quoteName('params') . '=' . $params)
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
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
     * Return the ID of the virtual currency.
     *
     * <code>
     * $currencyId = 1;
     *
     * $currency    = new Virtualcurrency\Currency\Currency(JFactory::getDbo());
     * $currency->load($currencyId);
     *
     * if (!$currency->getId()) {
     * ...
     * }
     * </code>
     *
     * @return  int
     */
    public function getId()
    {
        return (int)$this->id;
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
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
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
        return $this->code;
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
        return $this->symbol;
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
     * @return int
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
     * @return int
     */
    public function getIcon()
    {
        return $this->image_icon;
    }
}
