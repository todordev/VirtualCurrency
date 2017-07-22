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
use Virtualcurrency\Commodity\Command\Gateway\UpdateInStockGateway;
use Virtualcurrency\Commodity\Commodity;

/**
 * This is a command that updates the number of commodities in stock.
 *
 * @package      Virtualcurrency\Commodity
 * @subpackage   Command
 */
class UpdateInStock implements Command
{
    /**
     * @var UpdateInStockGateway
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

    public function setGateway(UpdateInStockGateway $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function handle()
    {
        $this->gateway->update($this->commodity);
    }
}
