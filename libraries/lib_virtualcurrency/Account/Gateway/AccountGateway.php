<?php
/**
 * @package         Virtualcurrency\Account
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Account\Gateway;

use Prism\Domain\RichFetcher;
use Virtualcurrency\Account\Account;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency\Account
 * @subpackage      Gateway
 */
interface AccountGateway extends RichFetcher
{
    /**
     * Insert a record to database.
     *
     * @param Account $object
     *
     * @return mixed
     */
    public function insertObject(Account $object);

    /**
     * Update a record in database.
     *
     * @param Account $object
     *
     * @return mixed
     */
    public function updateObject(Account $object);
}
