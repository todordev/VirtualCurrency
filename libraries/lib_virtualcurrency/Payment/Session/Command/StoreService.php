<?php
/**
 * @package      Virtualcurrency\Payment\Session
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment\Session\Command;

use Prism\Command\Command;
use Virtualcurrency\Payment\Session\Service;
use Virtualcurrency\Payment\Session\Command\Gateway\StoreServiceGateway;

/**
 * This class contains methods,
 * which are used for managing virtual bank account.
 *
 * @package      Virtualcurrency\Payment\Session
 * @subpackage   Command
 */
class StoreService implements Command
{
    /**
     * @var StoreServiceGateway
     */
    protected $gateway;

    /**
     * @var Service
     */
    protected $service;

    /**
     * StoreService constructor.
     *
     * <code>
     * $account = new Virtualcurrency\Payment\Session\Session;
     *
     * $command = new StoreService($account);
     * </code>
     *
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * <code>
     * $account = new Virtualcurrency\Payment\Session\Session;
     *
     * $command = new StoreService($account);
     * $command->setGateway(new JoomlaStoreServiceGateway(/JFactory::getDbo()));
     * </code>
     *
     * @param StoreServiceGateway $gateway
     *
     * @return self
     */
    public function setGateway(StoreServiceGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * <code>
     * $account = new Virtualcurrency\Payment\Session\Session;
     *
     * $command = new StoreService($account);
     * $command->setGateway(new JoomlaStoreServiceGateway(/JFactory::getDbo()));
     *
     * $command->handle();
     * </code>
     */
    public function handle()
    {
        $this->gateway->store($this->service);
    }
}
