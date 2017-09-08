<?php
/**
 * @package         Virtualcurrency\Transaction
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Transaction\Gateway;

use Prism\Domain\RichFetcher;
use Virtualcurrency\Transaction\Transaction;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency\Transaction
 * @subpackage      Gateway
 */
interface TransactionGateway extends RichFetcher
{
    public function insertObject(Transaction $object);
    public function updateObject(Transaction $object);
}
