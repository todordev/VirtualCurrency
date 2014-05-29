<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {
    $currency = $this->currencies->getCurrency($item->currency_id);
    ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td>
            <a href="<?php echo JRoute::_("index.php?option=com_virtualcurrency&view=transaction&layout=edit&id=" . (int)$item->id); ?>">
                <?php echo $this->escape($item->title); ?>
            </a>
        </td>
        <td class="center">
            <?php echo $currency->getAmountString($item->units); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $this->realCurrency->getAmountString($item->txn_amount); ?>
        </td>

        <td class="center hidden-phone">
            <?php echo $this->escape($item->sender); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $this->escape($item->receiver); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo JHtml::_('date', $item->txn_date, JText::_('DATE_FORMAT_LC3')); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $this->escape($item->service_provider); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->txn_id; ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $this->escape($item->txn_status); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>
	  