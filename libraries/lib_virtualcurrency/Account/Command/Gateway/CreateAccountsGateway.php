<?php
/**
 * @package         Virtualcurrency\Account
 * @subpackage      Command\Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Account\Command\Gateway;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency\Account
 * @subpackage      Command\Gateway
 */
interface CreateAccountsGateway
{
    /**
     * Create an account.
     *
     * @param int $userId
     */
    public function create($userId);
}
