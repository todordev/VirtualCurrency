<?php
/**
 * @package      Virtual Currency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<tr>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th class="title">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_USER', 'b.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap center hidden-phone">
        <?php echo JText::_("COM_VIRTUALCURRENCY_AMOUNT"); ?>
    </th>
    <th width="10%" class="nowrap center hidden-phone">
        <?php echo JText::_("COM_VIRTUALCURRENCY_CURRENCY"); ?>
    </th>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  