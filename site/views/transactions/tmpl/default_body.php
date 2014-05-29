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
        <td class="center">
            <?php echo $currency->getAmountString($item->units); ?>
        </td>
        <td class="center">
            <?php echo $this->realCurrency->getAmountString($item->txn_amount); ?>
        </td>

        <td class="center"><?php echo $this->escape($item->sender); ?></td>
        <td class="center"><?php echo $this->escape($item->receiver); ?></td>

        <td class="center">
            <?php echo JHtml::_('date', $item->txn_date, JText::_('DATE_FORMAT_LC3')); ?>
        </td>
        <td>
            <?php echo $item->txn_id; ?>
        </td>
        <td class="center">
            <?php echo $this->escape($item->txn_status); ?>
        </td>
        <td class="center">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>
	  