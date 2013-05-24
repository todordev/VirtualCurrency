<?php
/**
 * @package      Virtual Currency
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Virtual Currency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined('_JEXEC') or die; ?>
<?php 
    if(!empty($accounts)) {
        foreach($accounts as $account) { 
            $currency = array(
                "code"   => $account["code"],
                "symbol" => $account["symbol"],
            );
        ?>
        <p class="vcm-account-amount">
            <?php echo $account["title"]; ?> : <?php echo JHtml::_("virtualcurrency.amount", $account["amount"], $currency); ?>
        </p>
        <?php 
        }
    }
?>