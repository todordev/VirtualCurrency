<?php
/**
 * @package      Virtualcurrency\User\Commodity
 * @subpackage   Command
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\User\Commodity\Command;

use Prism\Command\Command;
use Virtualcurrency\User\Commodity\Command\Gateway\StoreNumberGateway;
use Virtualcurrency\User\Commodity\Commodity;

/**
 * This is a command that updates the number of user commodities.
 *
 * @package      Virtualcurrency\User\Commodity
 * @subpackage   Command
 */
class StoreNumber implements Command
{
    /**
     * @var StoreNumberGateway
     */
    protected $gateway;

    /**
     * @var Commodity $commodity
     */
    protected $commodity;

    public function __construct(Commodity $commodity)
    {
        $this->commodity = $commodity;
    }

    public function setGateway(StoreNumberGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->store($this->commodity);
    }
}
