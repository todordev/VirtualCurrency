<?php
/**
 * @package         Virtualcurrency\Transaction
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction\Gateway;

use Virtualcurrency\Transaction\Transaction;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency\Transaction
 * @subpackage      Gateway
 */
interface TransactionGateway
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

    public function insertObject(Transaction $object);
    public function updateObject(Transaction $object);
}
