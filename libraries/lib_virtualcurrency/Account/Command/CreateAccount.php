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
use Virtualcurrency\Account\Command\Gateway\CreateAccountGateway;

/**
 * This class contains methods,
 * which are used for managing virtual bank account.
 *
 * @package      Virtualcurrency\Account
 * @subpackage   Command
 */
class CreateAccount implements Command
{
    /**
     * @var CreateAccountGateway
     */
    protected $gateway;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var int
     */
    protected $currencyId;

    /**
     * CreateAccount constructor.
     *
     * <code>
     * $userId = 1;
     * $currencyId = 2;
     *
     * $command = new CreateAccount($userId, $currencyId);
     * </code>
     *
     * @param int $userId
     * @param int $currencyId
     */
    public function __construct($userId, $currencyId)
    {
        $this->userId     = (int)$userId;
        $this->currencyId = (int)$currencyId;
    }

    /**
     * <code>
     * $userId = 1;
     * $currencyId = 2;
     *
     * $command = new CreateAccount($userId, $currencyId);
     * $command->setGateway(new JoomlaCreateAccountGateway(/JFactory::getDbo()));
     * </code>
     *
     * @param CreateAccountGateway $gateway
     *
     * @return self
     */
    public function setGateway(CreateAccountGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * <code>
     * $userId = 1;
     * $currencyId = 2;
     *
     * $command = new CreateAccount($userId, $currencyId);
     * $command->setGateway(new JoomlaCreateAccountGateway(/JFactory::getDbo()));
     *
     * $command->handle();
     * </code>
     */
    public function handle()
    {
        $this->gateway->create($this->userId, $this->currencyId);
    }
}
