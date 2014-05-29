<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Virtual Currency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $item) {
    $currency = $this->currencies->getCurrency($item->currency_id);
    ?>
    <tr>
        <td>
            <?php echo $this->escape($item->title); ?>
        </td>
        <td>
            <?php echo $currency->getAmountString($item->amount); ?>
        </td>
    </tr>
<?php } ?>
	  