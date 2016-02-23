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
<tr>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th width="1%" style="min-width: 55px" class="nowrap center">
        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
    </th>
    <th class="title">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_TITLE', 'a.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="5%">
        &nbsp;
    </th>
    <th width="10%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_CURRENCY_CODE', 'a.code', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="20%" class="nowrap center hidden-phone">
        <?php echo JText::_('COM_VIRTUALCURRENCY_PRICE'); ?>
    </th>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JText::_("COM_VIRTUALCURRENCY_CURRENCY_SYMBOL"); ?>
    </th>
    <th width="3%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  