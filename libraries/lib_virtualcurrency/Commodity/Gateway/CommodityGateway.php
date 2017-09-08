<?php
/**
 * @package         Virtualcurrency/Commodity
 * @subpackage      Gateway
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Commodity\Gateway;

use Prism\Domain\RichFetcher;
use Virtualcurrency\Commodity\Commodity;

/**
 * Contract between database drivers and gateway objects.
 *
 * @package         Virtualcurrency/Commodity
 * @subpackage      Gateway
 */
interface CommodityGateway extends RichFetcher
{
    public function insertObject(Commodity $object);
    public function updateObject(Commodity $object);
}
