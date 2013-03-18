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
<?php foreach ($this->items as $i => $item) {
	    $ordering  = ($this->listOrder == 'a.ordering');
	?>
	<tr class="row<?php echo $i % 2; ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
		<td><?php echo $item->sender; ?></td>
		<td><?php echo $item->receiver; ?></td>
		<td><?php echo $item->amount; ?></td>
		<td><?php echo $item->currency; ?> [ <?php echo $item->currency_code; ?> ]</td>
		<td><?php echo JHtml::_('date', $item->record_date, JText::_('DATE_FORMAT_LC3')); ?></td>
        <td class="center"><?php echo $item->id;?></td>
	</tr>
<?php }?>
	  