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
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, "currencies."); ?>
        </td>
        <td>
            <a href="<?php echo JRoute::_("index.php?option=com_virtualcurrency&view=currency&layout=edit&id=" . $item->id); ?>">
                <?php echo $item->title; ?>
            </a>
        </td>
        <td class="center hidden-phone">
            <strong><?php echo $item->code; ?></strong>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->symbol; ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>
	  