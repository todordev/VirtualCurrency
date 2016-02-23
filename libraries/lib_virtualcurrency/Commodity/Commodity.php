<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Commodities
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Commodity;

use Prism\Database\Table;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Virtualcurrency
 * @subpackage   Commodities
 */
class Commodity extends Table
{
    protected $id;
    protected $title;
    protected $description;
    protected $number;
    protected $price;
    protected $price_virtual;
    protected $currency_id;
    protected $minimum;
    protected $image;
    protected $image_icon;
    protected $published;

    /**
     * Load commodity data from database.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity   = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.number, a.price, a.price_virtual, ' .
                'a.currency_id, a.minimum, a.published, a.image, a.image_icon'
            )
            ->from($this->db->quoteName('#__vc_commodities', 'a'));

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
     *  "title"    => "Keys",
     *  "description"  => "My description...",
     *  "number" => 100,
     *  "sold" => 0
     * );
     *
     * $commodity   = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->bind($data);
     * $commodity->store();
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

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_commodities'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('description') . '=' . $description)
            ->set($this->db->quoteName('number') . '=' . (int)$this->number)
            ->set($this->db->quoteName('price') . '=' . $this->db->quote($this->price))
            ->set($this->db->quoteName('price_virtual') . '=' . $this->db->quote($this->price_virtual))
            ->set($this->db->quoteName('currency_id') . '=' . $this->currency_id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $description   = (!$this->description) ? 'NULL' : $this->db->quote($this->description);

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_commodities'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('description') . '=' . $description)
            ->set($this->db->quoteName('number') . '=' . (int)$this->number)
            ->set($this->db->quoteName('price') . '=' . $this->db->quote($this->price))
            ->set($this->db->quoteName('price_virtual') . '=' . $this->db->quote($this->price_virtual))
            ->set($this->db->quoteName('currency_id') . '=' . (int)$this->currency_id)
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * This method calculates the price in real currency of the units.
     * You have to give the number of your units that you would like to calculate.
     *
     * <code>
     *  $commodityId  = 1;
     *
     *  $commodity    = Virtualcurrency\Commodity\Commodity::getInstance(JFactory::getDbo, $commodityId);
     *
     *  // It is the number of units, that I would like to buy.
     *  $unitsNumber = 10;
     *  $amount      = $commodity->calculateRealAmount($unitsNumber);
     * </code>
     *
     * @param  integer $units
     *
     * @return float Amount
     */
    public function calculateRealAmount($units)
    {
        $amount = 0;
        if ($units > 0) {
            $amount = $this->price;
            $amount *= $units;
        }

        return $amount;
    }

    /**
     * This method calculates the price in virtual currency of the units.
     * You have to give the number of your units that you would like to calculate.
     *
     * <code>
     *  $commodityId  = 1;
     *
     *  $commodity    = Virtualcurrency\Commodity\Commodity::getInstance(JFactory::getDbo, $commodityId);
     *
     *  // It is the number of units, that I would like to buy.
     *  $unitsNumber = 10;
     *  $amount      = $commodity->calculateVirtualAmount($unitsNumber);
     * </code>
     *
     * @param  integer $units
     *
     * @return float Amount
     */
    public function calculateVirtualAmount($units)
    {
        $amount = 0;
        if ($units > 0) {
            $amount = $this->price_virtual;
            $amount *= $units;
        }

        return $amount;
    }

    /**
     * Return the ID of the item.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * if (!$commodity->getId()) {
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
     * Return the description of the unit.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * $description = $commodity->getDescription();
     * </code>
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return number of the units in stock.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * if (!$commodity->getNumber()) {
     * ...
     * }
     * </code>
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Return the title of the unit.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * $title = $commodity->getTitle();
     * </code>
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
     * <code>
     * $commodityId  = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(\JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * echo $commodity->getImage();
     * </code>
     *
     * @return int
     */
    public function getImage()
    {
        return $this->image;
    }


    /**
     * Return the icon of the commodity.
     *
     * <code>
     * $commodityId  = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(\JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * echo $commodity->getIcon();
     * </code>
     *
     * @return int
     */
    public function getIcon()
    {
        return $this->image_icon;
    }

    /**
     * Return number of minimum units that users can buy in an order.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * if (100 < $commodity->getMinimum()) {
     * ...
     * }
     * </code>
     *
     * @return string
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * Return the price of the commodity in real currency.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * if (!$commodity->getPrice()) {
     * ...
     * }
     * </code>
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Return the price of the commodity in virtual currency.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * if (!$commodity->getPriceVirtual()) {
     * ...
     * }
     * </code>
     *
     * @return string
     */
    public function getPriceVirtual()
    {
        return $this->price_virtual;
    }

    /**
     * Return the ID of the virtual currency in which this commodity can be sold.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * $currencyId = $commodity->getCurrencyId();
     * </code>
     *
     * @return int
     */
    public function getCurrencyId()
    {
        return (int)$this->currency_id;
    }
}
