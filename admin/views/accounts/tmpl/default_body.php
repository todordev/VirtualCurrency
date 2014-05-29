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
<?php foreach ($this->items as $i => $item) { ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td>
            <a href="<?php echo JRoute::_("index.php?option=com_virtualcurrency&view=account&layout=edit&id=" . (int)$item->id); ?>"><?php echo $item->name; ?></a>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->amount; ?>
        </td>
        <td class="center hidden-phone">
            <a href="<?php echo JRoute::_("index.php?option=com_virtualcurrency&view=currency&layout=edit&id=" . (int)$item->currency_id); ?>"><?php echo $item->currency_title; ?>
                [ <?php echo $item->currency_code; ?> ] </a>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>
	  