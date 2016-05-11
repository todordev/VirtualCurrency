<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td>
            <a href="<?php echo JRoute::_('index.php?option=com_virtualcurrency&view=transaction&layout=edit&id=' . (int)$item->id); ?>">
                <?php echo $this->escape($item->title); ?>
            </a>
        </td>
        <td>
            <?php echo $item->units; ?>
        </td>
        <td class="hidden-phone">
            <?php echo $item->txn_amount . ' ' . $item->txn_currency; ?>
        </td>

        <td class="hidden-phone">
            <?php echo ($item->sender) ?: JText::_('COM_VIRTUALCURRENCY_BANK'); ?>
        </td>
        <td class="hidden-phone">
            <?php echo $this->escape($item->receiver); ?>
        </td>
        <td class="hidden-phone">
            <?php echo JHtml::_('date', $item->txn_date, JText::_('DATE_FORMAT_LC3')); ?>
        </td>
        <td class="hidden-phone">
            <?php echo $this->escape($item->service_provider); ?>
        </td>
        <td class="hidden-phone">
            <?php echo $this->escape($item->txn_id); ?>
        </td>
        <td class="hidden-phone">
            <?php echo $this->escape($item->txn_status); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo (int)$item->id; ?>
        </td>
    </tr>
<?php } ?>
	  