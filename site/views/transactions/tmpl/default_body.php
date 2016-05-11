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
<?php foreach ($this->items as $item) {?>
    <tr>
        <td>
            <?php echo $this->escape($item->title); ?>
        </td>
        <td>
            <?php echo $item->units; ?>
        </td>
        <td>
            <?php echo $item->txn_amount; ?>
        </td>

        <td><?php echo (!$item->sender) ? JText::_('COM_VIRTUALCURRENCY_BANK') : $this->escape($item->sender); ?></td>
        <td><?php echo $this->escape($item->receiver); ?></td>

        <td>
            <?php echo JHtml::_('date', $item->txn_date, JText::_('DATE_FORMAT_LC3')); ?>
        </td>
        <td>
            <?php echo $this->escape($item->txn_id); ?>
        </td>
        <td>
            <?php echo $this->escape($item->txn_status); ?>
        </td>
        <td class="center">
            <?php echo (int)$item->id; ?>
        </td>
    </tr>
<?php } ?>
