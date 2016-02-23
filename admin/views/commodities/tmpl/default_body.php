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
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'commodities.'); ?>
        </td>
        <td>
            <a href="<?php echo JRoute::_('index.php?option=com_virtualcurrency&view=commodity&layout=edit&id=' . $item->id); ?>">
                <?php echo $this->escape($item->title); ?>
            </a>
        </td>
        <td class="center hidden-phone">
            <?php if ($item->image_icon) { ?>
            <img src="<?php echo $this->mediaFolderUrl . '/' .$item->image_icon; ?>"  alt="<?php echo $this->escape($item->title); ?>"/>
            <?php } ?>
        </td>
        <td class="center hidden-phone">
            <strong><?php echo $item->number; ?></strong>
        </td>
        <td class="hidden-phone">
            <?php echo JHtml::_('virtualcurrency.virtualGoodsPrice', $item, $this->amount, $this->realCurrency, $this->currencies); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>
	  