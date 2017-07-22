<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Money
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Money;

use Virtualcurrency\Money\Helper\MoneyHelper;

/**
 * This is a helper class for money object.
 *
 * @package      Virtualcurrency
 * @subpackage   Money
 */
class Helper
{
    protected static $pool = array();

    /**
     * Return a helper depending of the environment.
     *
     * @param string $env
     *
     * @return MoneyHelper
     */
    public static function factory($env)
    {
        $helperClass = '\\Virtualcurrency\\Money\\Helper\\'.ucfirst(basename($env));

        if (array_key_exists($env, self::$pool)) {
            return self::$pool[$env];
        }

        self::$pool[$env] = new $helperClass;
        return self::$pool[$env];
    }
}
