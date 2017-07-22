<?php
/**
 * @package      Virtualcurrency
 * @subpackage   RealCurrency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\RealCurrency;

use Prism\Domain;
use Prism\Domain\Entity;
use Virtualcurrency\RealCurrency\Gateway\CurrencyGateway;

/**
 * This class provides functionality that manage the persistence of the real currency objects.
 *
 * @package      Virtualcurrency
 * @subpackage   RealCurrency
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
     * @param CurrencyGateway $gateway
     */
    public function __construct(CurrencyGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return a gateway object.
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
        // @todo Do insertObject method in the currency mapper.
    }

    protected function updateObject(Entity $object)
    {
        // @todo Do updateObject method in the currency mapper.
    }

    protected function deleteObject(Entity $object)
    {
        // @todo Do deleteObject method in the currency mapper.
    }
}
