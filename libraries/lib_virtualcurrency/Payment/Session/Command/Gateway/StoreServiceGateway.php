<?php
/**
 * @package         Virtualcurrency\Payment\Session\Command
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment\Session\Command\Gateway;

use Virtualcurrency\Payment\Session\Service;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency\Payment\Session\Command
 * @subpackage      Gateway
 */
interface StoreServiceGateway
{
    /**
     * Store payment gateway data.
     *
     * @param Service $service
     */
    public function store(Service $service);
}
