<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="vctransactions<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <form action="<?php echo JRoute::_('index.php?option=com_virtualcurrency&view=transactions'); ?>" method="post" name="adminForm" id="adminForm">

        <table class="table table-striped table-bordered vc-txns-results">
            <thead><?php echo $this->loadTemplate('head'); ?></thead>
            <tfoot><?php echo $this->loadTemplate('foot'); ?></tfoot>
            <tbody><?php echo $this->loadTemplate('body'); ?></tbody>
        </table>

        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="filter_order" value="<?php echo $this->listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirn; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>