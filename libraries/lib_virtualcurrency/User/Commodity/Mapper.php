<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Commodity
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\User\Commodity;

use Prism\Domain;
use Prism\Domain\Entity;
use Virtualcurrency\User\Commodity\Gateway\CommodityGateway;

/**
 * This class provides functionality that manage the persistence of the commodity objects.
 *
 * @package      Virtualcurrency
 * @subpackage   Commodity
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var CommodityGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Virtualcurrency\Commodity\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Virtualcurrency\Commodity\Mapper($gateway);
     * </code>
     *
     * @param CommodityGateway $gateway
     */
    public function __construct(CommodityGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
     *
     * <code>
     * $gateway = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Virtualcurrency\Account\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return CommodityGateway
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Populate an object.
     *
     * <code>
     * $currencyId = 1;
     *
     * $gateway  = new Virtualcurrency\Commodity\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($currencyId);
     *
     * $mapper   = new Virtualcurrency\Commodity\Mapper($gateway);
     * $currency = $mapper->populate(new Virtualcurrency\Commodity\Commodity, $data);
     * </code>
     *
     * @param Entity $object
     * @param array  $data
     *
     * @return Entity
     */
    public function populate(Entity $object, array $data)
    {
        $commodityData = array();
        foreach ($data as $columnName => $value) {
            if (strpos($columnName, 'c_') === 0) {
                $key = str_replace('c_', '', $columnName);
                $commodityData[$key] = $value;
                unset($data[$columnName]);
            }
        }

        $data['commodity'] = $commodityData;

        $object->bind($data);

        return $object;
    }

    protected function createObject()
    {
        return new Commodity;
    }

    protected function insertObject(Entity $object)
    {
        $this->gateway->insertObject($object);
    }

    protected function updateObject(Entity $object)
    {
        $this->gateway->updateObject($object);
    }

    protected function deleteObject(Entity $object)
    {
        // @todo Do deleteObject method in the currency mapper.
    }
}
