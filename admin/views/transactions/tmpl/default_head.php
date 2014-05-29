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
<tr>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th class="title">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_CURRENCY', 'b.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_UNITS', 'a.units', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap center">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_AMOUNT', 'a.txn_amount', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_SENDER', 'c.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_BENEFICIARY', 'd.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_DATE', 'a.txn_date', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_SERVICE', 'a.service_provider', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_TXN_ID', 'a.txn_id', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_STATUS', 'a.txn_status', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%1" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  