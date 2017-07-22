<?php
/**
 * @package      Virtualcurrency
 * @subpackage   RealCurrency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\RealCurrency;

use Prism\Domain\Collection;
use Prism\Domain\CollectionToOptions;
use Prism\Domain\ToOptionsMethod;

/**
 * This class provides functionality that manage real currencies.
 *
 * @package      Virtualcurrency
 * @subpackage   RealCurrency
 */
class Currencies extends Collection implements CollectionToOptions
{
    use ToOptionsMethod;

    protected $codeIndexed = array();

    public function fetchByCode($code)
    {
        // Create an array that provide an array indexed by the codes of the items.
        if (!$this->codeIndexed) {
            /** @var Currency $item */
            foreach ($this->items as $item) {
                $itemCode = $item->getCode();
                if (!$itemCode) {
                    continue;
                }

                $this->codeIndexed[$itemCode] = $item;
            }
        }

        return array_key_exists($code, $this->codeIndexed) ? $this->codeIndexed[$code] : null;
    }
}
