<?php
/**
 * @package      Virtual Currency
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */
 
// no direct access
defined('_JEXEC') or die; ?>
<?php
foreach ($accounts as $account) {
    $currency = $currencies->getCurrency($account['currency_id']);

    if ($currency !== null) {
        $amount->setCurrency($currency);
        ?>
        <p class="vcm-account-amount">
            <?php echo htmlentities($account['title'], ENT_QUOTES, 'UTF-8'); ?>
            : <?php echo $amount->setValue($account['amount'])->format(); ?>
        </p>
        <?php
    }
}
