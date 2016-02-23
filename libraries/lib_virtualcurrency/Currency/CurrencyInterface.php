<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Currencies
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Currency;

defined('JPATH_PLATFORM') or die;

/**
 * This is the currency interface.
 *
 * @package      Virtualcurrency
 * @subpackage   Currencies
 */
interface CurrencyInterface
{
    public function getId();
    public function getTitle();
    public function getCode();
    public function getSymbol();
    public function getPosition();
}
