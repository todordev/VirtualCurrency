<?php
/**
 * @package      Virtualcurrency\Payment
 * @subpackage   Session
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment\Session;

use Prism\Domain;
use Prism\Domain\Entity;
use Virtualcurrency\Payment\Session\Gateway\SessionGateway;

/**
 * This class provides functionality that manage the persistence of the payment session objects.
 *
 * @package      Virtualcurrency\Payment
 * @subpackage   Session
 */
class Mapper extends Domain\Mapper
{
    /**
     * @var SessionGateway
     */
    protected $gateway;

    /**
     * Initialize the object.
     *
     * <code>
     * $gateway = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * </code>
     *
     * @param SessionGateway $gateway
     */
    public function __construct(SessionGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Return gateway object.
     *
     * <code>
     * $gateway = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper  = new Virtualcurrency\Payment\Session\Mapper($gateway);
     *
     * $gateway = $mapper->getGateway();
     * </code>
     *
     * @return SessionGateway
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Populate an object.
     *
     * <code>
     * $paymentId = 1;
     *
     * $gateway  = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $data     = $gateway->fetchById($paymentId);
     *
     * $mapper   = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $session  = $mapper->populate(new Virtualcurrency\Payment\Session\Session, $data);
     * </code>
     *
     * @param Entity $object
     * @param array  $data
     *
     * @return Entity
     */
    public function populate(Entity $object, array $data)
    {
        if (array_key_exists('services', $data) and is_array($data['services']) and count($data['services']) > 0) {
            $services = new Services;
            foreach ($data['services'] as $key => $values) {
                $service = new Service;
                $service->bind($values);
                $services[$service->getAlias()] = $service;
            }

            $data['services'] = $services;
        }

        $object->bind($data);

        return $object;
    }

    protected function createObject()
    {
        return new Session;
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
        $this->gateway->deleteObject($object);
    }
}
