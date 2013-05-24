<?php
/**
 * @package      ITPrism Components
 * @subpackage   Virtual Currency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Virtual Currency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;
?>
<tr>
	<th>
	    <?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_TITLE', 'b.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th>
		<?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_NUMBER', 'a.number', $this->listDirn, $this->listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_AMOUNT', 'a.txn_amount', $this->listDirn, $this->listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_SENDER', 'c.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_BENEFICIARY', 'd.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_DATE', 'a.txn_date', $this->listDirn, $this->listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_TXN_ID', 'a.txn_id', $this->listDirn, $this->listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_STATUS', 'a.txn_status', $this->listDirn, $this->listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  