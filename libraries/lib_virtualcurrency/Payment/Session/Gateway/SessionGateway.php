<?php
/**
 * @package         Virtualcurrency\Payment\Session
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment\Session\Gateway;

use Virtualcurrency\Payment\Session\Session;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency\Payment\Session
 * @subpackage      Gateway
 */
interface SessionGateway
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

    public function insertObject(Session $object);
    public function updateObject(Session $object);
    public function deleteObject(Session $object);
}
