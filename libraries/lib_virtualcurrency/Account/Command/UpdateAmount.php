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
use Virtualcurrency\Account\Account;
use Virtualcurrency\Account\Command\Gateway\UpdateAmountGateway;

/**
 * This class contains methods,
 * which are used for managing virtual bank account.
 *
 * @package      Virtualcurrency\Account
 * @subpackage   Command
 */
class UpdateAmount implements Command
{
    /**
     * @var UpdateAmountGateway
     */
    protected $gateway;

    /**
     * @var Account
     */
    protected $account;

    /**
     * UpdateAmount constructor.
     *
     * <code>
     * $account = new Virtualcurrency/Account/Account;
     *
     * $command = new UpdateAmount($account);
     * </code>
     *
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account     = $account;
    }

    /**
     * <code>
     * $account = new Virtualcurrency/Account/Account;
     *
     * $command = new UpdateAmount($account);
     * $command->setGateway(new JoomlaUpdateAmountGateway(/JFactory::getDbo()));
     * </code>
     *
     * @param UpdateAmountGateway $gateway
     *
     * @return self
     */
    public function setGateway(UpdateAmountGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * <code>
     * $account = new Virtualcurrency/Account/Account;
     *
     * $command = new UpdateAmount($account);
     * $command->setGateway(new JoomlaUpdateAmountGateway(/JFactory::getDbo()));
     *
     * $command->handle();
     * </code>
     */
    public function handle()
    {
        $this->gateway->update($this->account);
    }
}
