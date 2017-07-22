<?php
/**
 * @package         Virtualcurrency\Account
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Account\Gateway;

use Virtualcurrency\Account\Account;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency\Account
 * @subpackage      Gateway
 */
interface AccountGateway
{
    /**
     * Return an item filtering by its ID.
     *
     * @param int $id
     *
     * @return array
     */
    public function fetchById($id);

    /**
     * Return an item filtering results by conditions.
     *
     * @param array $conditions
     *
     * @return array
     */
    public function fetch(array $conditions = array());

    /**
     * Return a collection of items filtering by conditions.
     *
     * @param array $conditions
     *
     * @return array
     */
    public function fetchCollection(array $conditions = array());

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
