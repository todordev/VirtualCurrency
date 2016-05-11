<?php
/**
 * @package      Virtual Currency
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
        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
    </th>
    <th class="title">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_NAME', 'b.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap">
        <?php echo JHtml::_('searchtools.sort', 'COM_VIRTUALCURRENCY_AMOUNT', 'a.amount', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="20%" class="nowrap center">
        <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_CREATED_DATE', 'a.created_at', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
