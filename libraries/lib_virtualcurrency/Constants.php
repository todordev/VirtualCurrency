<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Constants
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency;

/**
 * Virtualcurrency constants.
 *
 * @package      Virtualcurrency
 * @subpackage   Constants
 */
class Constants
{
    // Logs
    const ENABLE_SYSTEM_LOG = true;
    const DISABLE_SYSTEM_LOG = false;

    // Project states
    const MULTIPLICATION = 'M';
    const SUM = 'S';

    const PAYMENT_SESSION_CONTEXT = 'vcpaymentsession';

    const BANK_ID = 0;

    // Containers
    const CONTAINER_REAL_CURRENCY = 'container_real_currency';
    const CONTAINER_FORMATTER_MONEY = 'container_formatter_money';
    const CONTAINER_PARSER_MONEY = 'container_parser_money';
    const CONTAINER_FORMATTER_NUMBER = 'container_formatter_number';
    const CONTAINER_PARSER_NUMBER = 'container_parser_number';
}
