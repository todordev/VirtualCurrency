<?php
/**
 * @package      ITPrism Components
 * @subpackage   Virtual Currency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Virtual Currency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach($this->items as $item) {
    $currency = $this->currencies->getCurrency($item->currency_id);
?>
<tr>
	<td>
		<?php echo $this->escape($item->title); ?>
    </td>
	<td class="center">
	    <?php echo JHtml::_("virtualcurrency.amount", $item->number, $currency); ?>
    </td>
    <td class="center">
	    <?php echo $item->txn_amount." ".$item->txn_currency; ?>
    </td>
    
    <td class="center"><?php echo $item->sender; ?></td>
	<td class="center"><?php echo $item->receiver; ?></td>
	
	<td class="center">
	    <?php echo JHtml::_('date', $item->txn_date, JText::_('DATE_FORMAT_LC3')); ?>
    </td>
	<td>
	    <?php echo $item->txn_id; ?>
    </td>
	<td class="center">
	    <?php echo $item->txn_status; ?>
    </td>
	<td class="center">
		<?php echo $item->id; ?>
	</td>
</tr>
<?php }?>
	  