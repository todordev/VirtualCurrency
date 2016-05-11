<?php
/**
 * @package      Virtualcurrency
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
    <th class="title">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_TITLE', 'a.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_UNITS', 'a.units', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_AMOUNT', 'a.txn_amount', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_SENDER', 'c.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_BENEFICIARY', 'd.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_DATE', 'a.txn_date', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_SERVICE', 'a.service_provider', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_TXN_ID', 'a.txn_id', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%10" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_STATUS', 'a.txn_status', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="%1" class="nowrap center hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
