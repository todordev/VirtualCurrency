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
<?php foreach ($this->items as $i => $item) {
    $ordering = ($this->listOrder == 'a.ordering');
    ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
        <td>
            <a href="<?php echo JRoute::_("index.php?option=com_virtualcurrency&view=partner&layout=edit&id=" . $item->id); ?>"><?php echo $item->title; ?></a>
        </td>
        <td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, "partners."); ?></td>
        <td class="center"><?php echo $item->id; ?></td>
    </tr>
<?php } ?>
	  