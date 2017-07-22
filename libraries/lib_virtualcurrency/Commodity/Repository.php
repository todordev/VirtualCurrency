<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Commodity
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Commodity;

use Prism\Domain;
use Virtualcurrency\Commodity\Gateway\CommodityGateway;

/**
 * This class provides a glue between persistence layer and commodity object.
 *
 * @package      Virtualcurrency
 * @subpackage   Commodity
 */
class Repository extends Domain\Repository implements Domain\CollectionFetcher
{
    /**
     * Collection object.
     *
     * @var Commodities
     */
    protected $collection;

    /**
     * @var Gateway\JoomlaGateway
     */
    protected $gateway;

    /**
     * Repository constructor.
     *
     * <code>
     * $accountId  = 1;
     *
     * $gateway     = new Virtualcurrency\Account\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Account\Mapper($gateway);
     * $repository  = new Virtualcurrency\Account\Repository($mapper);
     * </code>
     *
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper  = $mapper;
        $this->gateway = $mapper->getGateway();
    }

    /**
     * Load the data from database and return an entity.
     *
     * <code>
     * $commodityId  = 1;
     *
     * $gateway     = new Virtualcurrency\Commodity\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Commodity\Mapper($gateway);
     * $repository  = new Virtualcurrency\Commodity\Repository($mapper);
     *
     * $commodity   = $repository->findById($commodityId);
     * </code>
     *
     * @param int $id
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return Commodity
     */
    public function fetchById($id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('There is no ID.');
        }

        $data = $this->gateway->fetchById($id);

        return $this->mapper->create($data);
    }

    /**
     * Load the data from database by conditions and return an entity.
     *
     * <code>
     * $conditions = array(
     *     'id' => 1,
     *     'published' => Prism\Constants::PUBLISHED
     * );
     *
     * $gateway     = new Virtualcurrency\Commodity\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Commodity\Mapper($gateway);
     * $repository  = new Virtualcurrency\Commodity\Repository($mapper);
     *
     * $commodity   = $repository->fetch($conditions);
     * </code>
     *
     * @param array  $conditions
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Domain\Entity
     */
    public function fetch(array $conditions = array())
    {
        if (!$conditions) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetch($conditions);

        return $this->mapper->create($data);
    }

    /**
     * Load the data from database and return a collection.
     *
     * <code>
     * $conditions = array(
     *     'ids' => array(1,2,3,4)
     * );
     *
     * $gateway     = new Virtualcurrency\Commodity\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Commodity\Mapper($gateway);
     * $repository  = new Virtualcurrency\Commodity\Repository($mapper);
     *
     * $commodities = $repository->fetchCollection($conditions);
     * </code>
     *
     * @param array  $conditions
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Domain\Collection
     */
    public function fetchCollection(array $conditions = array())
    {
        if (!$conditions) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetchCollection($conditions);

        if ($this->collection === null) {
            $this->collection = new Commodities;
        }

        $this->collection->clear();
        if ($data) {
            foreach ($data as $row) {
                $this->collection[] = $this->mapper->create($row);
            }
        }

        return $this->collection;
    }

    /**
     * Load the data from database and return a collection.
     *
     * <code>
     * $gateway     = new Virtualcurrency\Commodity\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Commodity\Mapper($gateway);
     * $repository  = new Virtualcurrency\Commodity\Repository($mapper);
     *
     * $currencies  = $repository->fetchAll($conditions);
     * </code>
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Commodities
     */
    public function fetchAll()
    {
        $data = $this->gateway->fetchAll();

        if ($this->collection === null) {
            $this->collection = new Commodities;
        }

        $this->collection->clear();
        if ($data) {
            foreach ($data as $row) {
                $this->collection[] = $this->mapper->create($row);
            }
        }

        return $this->collection;
    }
}
