<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Currency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Currency;

use Prism\Domain;
use Prism\Domain\Entity;
use Virtualcurrency\Currency\Gateway\CurrencyGateway;

/**
 * This class provides functionality that manage the persistence of the currency objects.
 *
 * @package      Virtualcurrency
 * @subpackage   Currency
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var CurrencyGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Virtualcurrency\Currency\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Virtualcurrency\Currency\Mapper($gateway);
     * </code>
     *
     * @param CurrencyGateway $gateway
     */
    public function __construct(CurrencyGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
     *
     * <code>
     * $gateway = new Virtualcurrency\Currency\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Virtualcurrency\Currency\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return CurrencyGateway
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
     * $gateway  = new Virtualcurrency\Currency\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($currencyId);
     *
     * $mapper   = new Virtualcurrency\Currency\Mapper($gateway);
     * $currency = $mapper->populate(new Virtualcurrency\Currency\Currency, $data);
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
        return new Currency;
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
