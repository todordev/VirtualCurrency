<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * VirtualCurrency component constants.
 *
 * @package      VirtualCurrency
 * @subpackage   Library
 */
class VirtualCurrencyConstants
{
    // States
    const PUBLISHED   = 1;
    const UNPUBLISHED = 2;
    const TRASHED     = -2;

    // Mail modes - html and plain text.
    const MAIL_MODE_HTML       = true;
    const MAIL_MODE_PLAIN_TEXT = false;

    // Logs
    const ENABLE_SYSTEM_LOG  = true;
    const DISABLE_SYSTEM_LOG = false;

    // Project states
    const MULTIPLICATION     = "M";
    const SUM = "S";
}
