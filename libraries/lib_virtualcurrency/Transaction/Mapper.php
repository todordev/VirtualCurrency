<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Transaction
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction;

use Prism\Domain;
use Prism\Domain\Entity;
use Virtualcurrency\Transaction\Gateway\TransactionGateway;

/**
 * This class provides functionality that manage the persistence of the transaction objects.
 *
 * @package      Virtualcurrency
 * @subpackage   Transaction
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var TransactionGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Virtualcurrency\Transaction\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Virtualcurrency\Transaction\Mapper($gateway);
     * </code>
     *
     * @param TransactionGateway $gateway
     */
    public function __construct(TransactionGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
     *
     * <code>
     * $gateway = new Virtualcurrency\Transaction\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Virtualcurrency\Transaction\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return TransactionGateway
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
     * $gateway  = new Virtualcurrency\Transaction\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($currencyId);
     *
     * $mapper   = new Virtualcurrency\Transaction\Mapper($gateway);
     * $currency = $mapper->populate(new Virtualcurrency\Transaction\Transaction, $data);
     * </code>
     *
     * @param Entity $object
     * @param array  $data
     *
     * @return Entity
     */
    public function populate(Entity $object, array $data)
    {
        $object->bind($data);

        return $object;
    }

    protected function createObject()
    {
        return new Transaction;
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
