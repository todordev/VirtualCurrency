<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $item) {
    $currency = $this->currencies->getCurrency($item->currency_id);
    if ($currency === null) {
        continue;
    }

    $this->amountFormatter->setCurrency($currency);
    ?>
    <tr>
        <td>
            <?php echo $this->escape($item->title); ?>
        </td>
        <td>
            <?php echo $this->amountFormatter->setValue($item->amount)->formatCurrency(); ?>
        </td>
    </tr>
<?php } ?>
	  