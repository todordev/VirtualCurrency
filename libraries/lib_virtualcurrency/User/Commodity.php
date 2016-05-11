<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Commodities
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\User;

use Prism\Database;
use Virtualcurrency\Commodity\Commodity as CommodityUnit;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Virtualcurrency
 * @subpackage   Commodities
 */
class Commodity extends Database\Table
{
    protected $id = 0;
    protected $title;
    protected $description;
    protected $number = 0;
    protected $image;
    protected $image_icon;
    protected $published;
    protected $user_id = 0;
    protected $commodity_id = 0;

    /**
     * @var CommodityUnit
     */
    protected $commodity;

    /**
     * Load commodity data from database.
     *
     * <code>
     * $keys = array(
     *     'user_id' => 1,
     *     'commodity_id' => 2
     * );
     *
     * $commodity   = new Virtualcurrency\User\Commodity(JFactory::getDbo());
     * $commodity->load($keys);
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
                'a.id, a.commodity_id, a.user_id, a.number, ' .
                'b.title, b.description, b.published, b.image, b.image_icon'
            )
            ->from($this->db->quoteName('#__vc_usercommodities', 'a'))
            ->rightJoin($this->db->quoteName('#__vc_commodities', 'b') . ' ON a.commodity_id = b.id');

        if (!is_array($keys)) {
            $query->where('a.id = ' . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) .' = ' . $this->db->quote($value));
            }
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
     *     "user_id"        => 1,
     *     "commodity_id"   => 2,
     *     "number"         => 100
     * );
     *
     * $commodity   = new Virtualcurrency\User\Commodity(JFactory::getDbo());
     * $commodity->bind($data);
     * $commodity->store();
     * </code>
     */
    public function store()
    {
        if (!(int)$this->user_id) {
            throw new \InvalidArgumentException('It is missing user ID.');
        }

        if (!(int)$this->commodity_id) {
            throw new \InvalidArgumentException('It is missing commodity ID.');
        }

        if (!$this->id) { // Insert
            $this->insertObject();
        } else { // Update
            $this->updateObject();
        }
    }

    protected function insertObject()
    {
        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName('#__vc_usercommodities'))
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('commodity_id') . '=' . (int)$this->commodity_id)
            ->set($this->db->quoteName('number') . '=' . (int)$this->number);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_usercommodities'))
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('commodity_id') . '=' . (int)$this->commodity_id)
            ->set($this->db->quoteName('number') . '=' . (int)$this->number)
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Return the ID of the item.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\User\Commodity(JFactory::getDbo());
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
        return $this->id;
    }

    /**
     * Return the description of the unit.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\User\Commodity(JFactory::getDbo());
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
     * Return number of the units owned by a user.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\User\Commodity(JFactory::getDbo());
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
     * Return the commodity.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\User\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * echo $commodity->getSold();
     * </code>
     *
     * @return CommodityUnit
     */
    public function getCommodity()
    {
        if ($this->commodity === null) {
            $this->commodity = new CommodityUnit($this->db);

            if ((int)$this->commodity_id > 0) {
                $this->commodity->load($this->commodity_id);
            }
        }

        return $this->commodity;
    }

    /**
     * Return the ID of the commodity.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\User\Commodity(JFactory::getDbo());
     * $commodity->load($commodityId);
     *
     * echo $commodity->getCommodityId();
     * </code>
     *
     * @return string
     */
    public function getCommodityId()
    {
        return $this->commodity_id;
    }

    /**
     * Return the title of the unit.
     *
     * <code>
     * $commodityId = 1;
     *
     * $commodity    = new Virtualcurrency\User\Commodity(JFactory::getDbo());
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
     * $commodity    = new Virtualcurrency\User\Commodity(\JFactory::getDbo());
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
     * $commodity    = new Virtualcurrency\User\Commodity(\JFactory::getDbo());
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
     * Increase the number of units.
     *
     * <code>
     *  // Get user commodity.
     *  $commodityId = 1;
     *
     *  $commodity   = new Virtualcurrency\Account\Account(JFactory::getDbo());
     *  $commodity->load($commodityId);
     *
     *  // Increase the number of commodities.
     *  $commodity->increaseNumber(50);
     *  $commodity->storeNumber();
     * </code>
     *
     * @param float $value
     *
     * @return self
     */
    public function increaseNumber($value)
    {
        if (is_numeric($value)) {
            $this->number += $value;
        }

        return $this;
    }

    /**
     * Decrease the number of units.
     *
     * <code>
     *  // Get user's commodity.
     *  $commodityId = 1;
     *
     *  $commodity   = new Virtualcurrency\User\Commodity(JFactory::getDbo());
     *  $commodity->load($commodityId);
     *
     *  // Increase the number of commodities.
     *  $commodity->decreaseAmount(50);
     *  $commodity->storeNumber();
     * </code>
     *
     * @param float $value
     *
     * @return self
     */
    public function decreaseNumber($value)
    {
        if (is_numeric($value)) {
            $this->number -= $value;
        }

        return $this;
    }

    /**
     * Store the number of units in database.
     *
     * <code>
     *  // Get user's commodity.
     *  $commodityId = 1;
     *
     *  $commodity   = new Virtualcurrency\User\Commodity(JFactory::getDbo());
     *  $commodity->load($commodityId);
     *
     *  // Increase the number of commodities.
     *  $commodity->increaseAmount(50);
     *  $commodity->storeNumber();
     * </code>
     */
    public function storeNumber()
    {
        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName('#__vc_usercommodities'))
            ->set($this->db->quoteName('number') . '=' . $this->db->quote($this->number))
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Return the price in real or virtual currency.
     *
     * <code>
     *  $commodityId = 1;
     *  $numberOfUnits = 10;
     *
     *  $commodity   = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     *  $commodity->load($commodityId);
     *
     *  echo $commodity->getRealPrice($numberOfUnits);
     * </code>
     *
     * @param string $type The type of the currency - real or virtual.
     * @param int $numberOfUnits
     *
     * @return float
     *
     * @deprecated v2.2
     */
    public function getPrice($type, $numberOfUnits = 1)
    {
        if (strcmp('real', $type) === 0) {
            $price = $this->commodity->getParam('price', 0.00);
            if ($price > 0 and $numberOfUnits > 0) {
                return round($price * $numberOfUnits, 2);
            }
        }

        if (strcmp('virtual', $type) === 0) {
            $price = $this->commodity->getParam('price-virtual', 0.00);
            if ($price > 0 and $numberOfUnits > 0) {
                return round($price * $numberOfUnits, 2);
            }
        }

        return 0.00;
    }

    /**
     * Calculate the price in real currency.
     *
     * <code>
     *  $commodityId = 1;
     *  $numberOfUnits = 10;
     *
     *  $commodity   = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     *  $commodity->load($commodityId);
     *
     *  echo $commodity->calculateRealPrice($numberOfUnits);
     * </code>
     *
     * @param int $numberOfUnits
     *
     * @return float
     */
    public function calculateRealPrice($numberOfUnits)
    {
        $price = $this->commodity->getParam('price_real', 0.00);
        if ($price > 0 and $numberOfUnits > 0) {
            return round($price * $numberOfUnits, 2);
        }

        return 0.00;
    }

    /**
     * Return the price in virtual currency.
     *
     * <code>
     *  $commodityId = 1;
     *  $numberOfUnits = 10;
     *
     *  $commodity   = new Virtualcurrency\Commodity\Commodity(JFactory::getDbo());
     *  $commodity->load($commodityId);
     *
     *  echo $commodity->calculateVirtualPrice($numberOfUnits);
     * </code>
     *
     * @param int $numberOfUnits
     *
     * @return float
     */
    public function calculateVirtualPrice($numberOfUnits)
    {
        $price = $this->commodity->getParam('price_virtual', 0.00);
        if ($price > 0 and $numberOfUnits > 0) {
            return round($price * $numberOfUnits, 2);
        }

        return 0.00;
    }
}
