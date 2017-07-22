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
use Virtualcurrency\Payment\Session\Gateway\SessionGateway;

/**
 * This class provides a glue between persistence layer and payment session object.
 *
 * @package      Virtualcurrency\Payment
 * @subpackage   Session
 */
class Repository extends Domain\Repository
{
    /**
     * @var SessionGateway
     */
    protected $gateway;

    /**
     * Set database gateway.
     *
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper  = $mapper;
        $this->gateway = $mapper->getGateway();
    }

    /**
     * Save session entity to database records.
     *
     * <code>
     * $sessionId  = 1;
     *
     * $gateway     = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository  = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setMapper($mapper);
     *
     * $session     = $repository->fetchById($sessionId);
     *
     * $session->setProjectId(1);
     * $session->setUserId(2);
     * $session->setRewardId(3);
     *
     * $repository->store($session);
     * </code>
     *
     * @param Session $entity
     *
     * @throws \InvalidArgumentException
     */
    public function store(Session $entity)
    {
        $this->mapper->save($entity);
    }

    /**
     * Remove session entity from database records.
     *
     * <code>
     * $sessionId  = 1;
     *
     * $gateway     = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $mapper      = new Virtualcurrency\Payment\Session\Mapper($gateway);
     * $repository  = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setMapper($mapper);
     *
     * $session     = $repository->fetchById($sessionId);
     *
     * $repository->delete($sessionId);
     * </code>
     *
     * @param Session $entity
     *
     * @throws \InvalidArgumentException
     */
    public function delete(Session $entity)
    {
        $this->mapper->delete($entity);
    }

    /**
     * Load the data from database and return an entity.
     *
     * <code>
     * $sessionId  = 1;
     *
     * $gateway     = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $repository  = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $session     = $repository->findById($sessionId);
     * </code>
     *
     * @param int $id
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return Session
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
     *     'code' => 'USD',
     *     'symbol' => '$'
     * );
     *
     * $gateway     = new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(\JFactory::getDbo());
     * $repository  = new Virtualcurrency\Payment\Session\Repository;
     * $repository->setGateway($gateway);
     *
     * $session    = $repository->fetch($conditions);
     * </code>
     *
     * @param array  $conditions
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return Session
     */
    public function fetch(array $conditions = array())
    {
        if (!$conditions) {
            throw new \UnexpectedValueException('There are no conditions that the system should use to fetch data.');
        }

        $data = $this->gateway->fetch($conditions);

        return $this->mapper->create($data);
    }
}
