<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Commodities
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Commodity;

use Prism\Database;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Virtualcurrency
 * @subpackage   Commodities
 */
class Commodity extends Database\Table
{
    protected $id;
    protected $title;
    protected $description;
    protected $in_stock;
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
            ->select('a.id, a.title, a.in_stock, a.published, a.image, a.image_icon, a.params')
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
        $params        = (!$this->params) ? 'NULL' : $this->db->quote($this->params->toString());
        $inStock       = (!is_numeric($this->in_stock)) ? 'NULL' : (int)$this->in_stock;

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_commodities'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('description') . '=' . $description)
            ->set($this->db->quoteName('in_stock') . '=' . $inStock)
            ->set($this->db->quoteName('params') . '=' . $params);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $description   = (!$this->description) ? 'NULL' : $this->db->quote($this->description);
        $params        = (!$this->params) ? 'NULL' : $this->db->quote($this->params->toString());
        $inStock       = (!is_numeric($this->in_stock)) ? 'NULL' : (int)$this->in_stock;

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_commodities'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('description') . '=' . $description)
            ->set($this->db->quoteName('in_stock') . '=' . $inStock)
            ->set($this->db->quoteName('params') . '=' . $params)
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Decrease the number of items in stock.
     *
     * <code>
     *  $commodityId  = 1;
     *  $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo);
     *  $commodity->load($commodityId);
     *
     *  // It is the number of units, that someone has bought.
     *  $unitsNumber = 10;
     *  $commodity->decreaseInStock($unitsNumber);
     * </code>
     *
     * @param  int $number
     *
     * @return self
     */
    public function decreaseInStock($number)
    {
        if ($this->in_stock !== null and $number > 0) {
            $this->in_stock -= (int)$number;
        }

        return $this;
    }

    /**
     * Store the number of items in stock.
     *
     * <code>
     *  $commodityId  = 1;
     *  $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo);
     *  $commodity->load($commodityId);
     *
     *  // It is the number of units, that someone has bought.
     *  $unitsNumber = 10;
     *  $commodity->decreaseInStock($unitsNumber);
     *  $commodity->storeInStock();
     * </code>
     *
     * @return self
     */
    public function storeInStock()
    {
        if ($this->in_stock !== null) {
            $query = $this->db->getQuery(true);
            $query
                ->update($this->db->quoteName('#__vc_commodities'))
                ->set($this->db->quoteName('in_stock') . '=' . (int)$this->in_stock)
                ->where($this->db->quoteName('id') . '=' . (int)$this->id);

            $this->db->setQuery($query);
            $this->db->execute();
        }
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
     * if (!$commodity->getInStock()) {
     * ...
     * }
     * </code>
     *
     * @return null|int
     */
    public function getInStock()
    {
        return $this->in_stock;
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
        return $this->params->get('minimum');
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
     * if (!$commodity->getPriceReal()) {
     * ...
     * }
     * </code>
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
        return $this->params->get('price_virtual');
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
        return (int)$this->params->get('currency_id');
    }

    /**
     * Check for enough units.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * if ($commodity->hasUnits()) {
     * // ....
     * }
     * </code>
     *
     * @param int $number
     *
     * @return bool
     */
    public function hasUnits($number = 0)
    {
        $number = (int)abs($number);

        if ($this->in_stock === null) {
            return true;
        }

        $inStock = (int)$this->in_stock;

        if ($inStock <= 0) {
            return false;
        } else {
            if ($number === 0) {
                return true;
            }

            if ($number < $inStock) {
                return true;
            }
        }

        return false;
    }
}
