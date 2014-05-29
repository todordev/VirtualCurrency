<?php
/**
 * @package      Virtual Currency
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 
// no direct access
defined('_JEXEC') or die; ?>
<?php
if(!empty($accounts)) {
    foreach ($accounts as $account) {
        $currency = $currencies->getCurrency($account["currency_id"]);
    ?>
    <p class="vcm-account-amount">
        <?php echo htmlspecialchars($account["title"]); ?> : <?php echo $currency->getAmountString($account["amount"]); ?>
    </p>
    <?php
    }
}
?>