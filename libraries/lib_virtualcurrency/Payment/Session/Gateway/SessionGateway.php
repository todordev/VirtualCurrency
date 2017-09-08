<?php
/**
 * @package         Virtualcurrency\Payment\Session
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment\Session\Gateway;

use Prism\Domain\Fetcher;
use Virtualcurrency\Payment\Session\Session;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency\Payment\Session
 * @subpackage      Gateway
 */
interface SessionGateway extends Fetcher
{
    public function insertObject(Session $object);
    public function updateObject(Session $object);
    public function deleteObject(Session $object);
}
