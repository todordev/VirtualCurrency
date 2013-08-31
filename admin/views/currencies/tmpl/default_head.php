<?php
/**
 * @package     Virtual Currency
 * @subpackage   Components
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
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th width="1%" style="min-width: 55px" class="nowrap center">
		<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="title">
	     <?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_TITLE', 'a.title', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="10%" class="nowrap center hidden-phone">
	   <?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_CURRENCY_CODE', 'a.code', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="1%" class="nowrap center hidden-phone">
	   <?php echo JText::_("COM_VIRTUALCURRENCY_CURRENCY_SYMBOL"); ?>
    </th>
    <th width="10%" class="nowrap center hidden-phone">
	   <?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_AMOUNT', 'a.amount', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap center hidden-phone">
	   <?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_REAL_CURRENCY', 'a.currency', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap center hidden-phone">
	   <?php echo JHtml::_('grid.sort',  'COM_VIRTUALCURRENCY_MIN_AMOUNT', 'a.minimum', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="3%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  