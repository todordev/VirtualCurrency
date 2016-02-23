<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
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
            <a href="<?php echo JRoute::_('index.php?option=com_virtualcurrency&view=realcurrency&layout=edit&id=' . (int)$item->id); ?>">
                <?php echo $item->title; ?>
            </a>
        </td>
        <td class="center"><a
                href="<?php echo JRoute::_('index.php?option=com_virtualcurrency&view=realcurrency&layout=edit&id=' . (int)$item->id); ?>">
                <?php echo $item->code; ?>
            </a>
        </td>
        <td class="center hidden-phone"><?php echo $item->symbol; ?></td>
        <td class="center hidden-phone"><?php echo $item->id; ?></td>
    </tr>
<?php } ?>
	  