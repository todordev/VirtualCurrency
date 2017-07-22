<?php
/**
 * @package      Virtualcurrency
 * @subpackage   RealCurrency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\RealCurrency;

use Prism\Domain\PropertiesMethods;
use Prism\Domain\Entity;
use Prism\Domain\EntityId;
use Prism\Domain\Populator;
use Prism\Money\CurrencyInterface;

/**
 * This class contains methods that are used for managing currency.
 *
 * @package      Virtualcurrency
 * @subpackage   RealCurrency
 */
class Currency implements Entity, CurrencyInterface
{
    use Populator, EntityId, PropertiesMethods;

    protected $title;
    protected $code;
    protected $symbol;
    protected $position;

    /**
     * Return the title of the real currency.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return currency code (abbreviation).
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return currency symbol.
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Return the position of currency symbol.
     *
     * @return int
     */
    public function getPosition()
    {
        return (int)$this->position;
    }
}
