<?php
/**
 * @package      Virtualcurrency\Commodity
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Commodity\Command;

use Prism\Command\Command;
use Virtualcurrency\Commodity\Command\Gateway\CreateCommoditiesGateway;

/**
 * This is a command that creates commodities records for a user.
 *
 * @package      Virtualcurrency\Commodity
 * @subpackage   Command
 */
class CreateCommodities implements Command
{
    /**
     * @var CreateCommoditiesGateway
     */
    protected $gateway;

    /**
     * @var int
     */
    protected $userId;

    public function __construct($userId = 0)
    {
        $this->userId = (int)$userId;
    }

    public function setGateway(CreateCommoditiesGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->create($this->userId);
    }
}
