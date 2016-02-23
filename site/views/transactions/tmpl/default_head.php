<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Virtual Currency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<tr>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_TITLE', 'a.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_UNITS', 'a.units', $this->listDirn, $this->listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_AMOUNT', 'a.txn_amount', $this->listDirn, $this->listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_SENDER', 'c.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_BENEFICIARY', 'd.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_DATE', 'a.txn_date', $this->listDirn, $this->listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_TXN_ID', 'a.txn_id', $this->listDirn, $this->listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'COM_VIRTUALCURRENCY_STATUS', 'a.txn_status', $this->listDirn, $this->listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  