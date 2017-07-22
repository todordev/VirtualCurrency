<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Currency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Currency;

use Prism\Domain\CollectionToOptions;
use Prism\Domain\PropertiesMethods;
use Prism\Domain\Collection;
use Prism\Domain\ToOptionsMethod;

/**
 * This class contains methods used for managing a set of currencies.
 *
 * @package      Virtualcurrency
 * @subpackage   Currency
 */
class Currencies extends Collection implements CollectionToOptions
{
    use PropertiesMethods, ToOptionsMethod;

    protected $codeIndexed = array();
    protected $idIndexed   = array();

    /**
     * @param $code
     *
     * @return Currency|null
     */
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

    /**
     * @param $currencyId
     *
     * @return Currency|null
     */
    public function fetchById($currencyId)
    {
        // Create an array that provide an array indexed by the codes of the items.
        if (!$this->idIndexed) {
            /** @var Currency $item */
            foreach ($this->items as $item) {
                $itemId = $item->getId();
                if (!$itemId) {
                    continue;
                }

                $this->idIndexed[$itemId] = $item;
            }
        }

        return array_key_exists($currencyId, $this->idIndexed) ? $this->idIndexed[$currencyId] : null;
    }
}
