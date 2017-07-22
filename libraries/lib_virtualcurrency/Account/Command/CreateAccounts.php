<?php
/**
 * @package      Virtualcurrency\Account
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Account\Command;

use Prism\Command\Command;
use Virtualcurrency\Account\Command\Gateway\CreateAccountsGateway;

/**
 * This class contains methods,
 * which are used for managing virtual bank account.
 *
 * @package      Virtualcurrency\Account
 * @subpackage   Command
 */
class CreateAccounts implements Command
{
    /**
     * @var CreateAccountsGateway
     */
    protected $gateway;

    /**
     * @var int
     */
    protected $userId;

    /**
     * CreateAccounts constructor.
     *
     * <code>
     * $userId = 1;
     *
     * $command = new CreateAccounts($userId);
     * </code>
     *
     * @param int $userId
     */
    public function __construct($userId = 0)
    {
        $this->userId = (int)$userId;
    }

    /**
     * <code>
     * $userId = 1;
     *
     * $command = new CreateAccounts($userId);
     * $command->setGateway(new JoomlaCreateAccountsGateway(/JFactory::getDbo()));
     * </code>
     *
     * @param CreateAccountsGateway $gateway
     *
     * @return self
     */
    public function setGateway(CreateAccountsGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * <code>
     * $userId = 1;
     *
     * $command = new CreateAccounts($userId);
     * $command->setGateway(new JoomlaCreateAccountsGateway(/JFactory::getDbo()));
     *
     * $command->handle();
     * </code>
     */
    public function handle()
    {
        $this->gateway->create($this->userId);
    }
}
