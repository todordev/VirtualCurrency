<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Constants
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency;

defined('JPATH_PLATFORM') or die;

/**
 * Virtualcurrency constants.
 *
 * @package      Virtualcurrency
 * @subpackage   Constants
 */
class Constants
{
    // Logs
    const ENABLE_SYSTEM_LOG  = true;
    const DISABLE_SYSTEM_LOG = false;

    // Project states
    const MULTIPLICATION     = 'M';
    const SUM = 'S';

    const PAYMENT_SESSION_CONTEXT = 'vcpaymentsession';

    const BANK_ID = 0;
}
