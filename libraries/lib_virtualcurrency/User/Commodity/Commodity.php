<?php
/**
 * @package      Virtualcurrency\User
 * @subpackage   Commodity
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\User\Commodity;

use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Virtualcurrency\Commodity\Commodity as CommodityUnit;

/**
 * This class contains methods that are used for managing commodity.
 *
 * @package      Virtualcurrency\User
 * @subpackage   Commodity
 */
class Commodity implements Entity
{
    use EntityId;

    protected $number = 0;
    protected $user_id = 0;
    protected $commodity_id = 0;

    /**
     * @var CommodityUnit
     */
    protected $commodity;

    /**
     * Set notification data to object parameters.
     *
     * <code>
     * $data = array(
     *      "amount"          => 100,
     *      "note"            => "...",
     *      "currency_id"     => 1
     *      "user_id"         => 2
     * );
     *
     * $account   = new Virtualcurrency\Account\Account;
     * $account->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind(array $data, array $ignored = array())
    {
        // Create Commodity object.
        if (array_key_exists('commodity', $data)) {
            $this->commodity = new CommodityUnit();
            $this->commodity->bind($data['commodity']);
            unset($data['commodity']);
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored, true)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return number of the units owned by a user.
     *
     * @return int
     */
    public function getNumber()
    {
        return (int)$this->number;
    }

    /**
     * Return the commodity.
     *
     * @return CommodityUnit
     */
    public function getCommodity()
    {
        if ($this->commodity === null) {
            $this->commodity = new CommodityUnit();
        }

        return $this->commodity;
    }

    /**
     * Return user ID.
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Return the ID of the commodity.
     *
     * @return int
     */
    public function getCommodityId()
    {
        return (int)$this->commodity_id;
    }

    /**
     * Increase the number of units.
     *
     * @param float $value
     *
     * @return self
     */
    public function increaseNumber($value)
    {
        if ($value > 0) {
            $this->number += $value;
        }

        return $this;
    }

    /**
     * Decrease the number of units.
     *
     * @param float $value
     *
     * @return self
     */
    public function decreaseNumber($value)
    {
        if ($this->number >= $value) {
            $this->number -= $value;
        }

        return $this;
    }
}
