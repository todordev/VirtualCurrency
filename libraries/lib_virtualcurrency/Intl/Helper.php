<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Intl
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Intl;

use Virtualcurrency\Intl\Helper\IntlHelper;

/**
 * This is a helper class for money object.
 *
 * @package      Virtualcurrency
 * @subpackage   Intl
 */
class Helper
{
    protected static $pool = array();

    /**
     * Return a helper depending of the environment.
     *
     * @param string $env
     *
     * @return IntlHelper
     */
    public static function factory($env)
    {
        $helperClass = '\\Virtualcurrency\\Intl\\Helper\\'.ucfirst(basename($env));

        if (array_key_exists($env, self::$pool)) {
            return self::$pool[$env];
        }

        self::$pool[$env] = new $helperClass;
        return self::$pool[$env];
    }
}
