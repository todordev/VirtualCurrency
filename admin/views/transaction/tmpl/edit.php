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

<form action="<?php echo JRoute::_('index.php?option=com_virtualcurrency'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="row-fluid">
        <div class="span12 form-horizontal">

            <?php echo $this->form->renderField('title'); ?>
            <?php echo $this->form->renderField('units'); ?>
            <?php echo $this->form->renderField('txn_id'); ?>
            <?php echo $this->form->renderField('txn_amount'); ?>
            <?php echo $this->form->renderField('txn_currency'); ?>
            <?php echo $this->form->renderField('txn_status'); ?>
            <?php echo $this->form->renderField('txn_date'); ?>
            <?php echo $this->form->renderField('service_provider'); ?>
            <?php echo $this->form->renderField('sender_id'); ?>
            <?php echo $this->form->renderField('receiver_id'); ?>
            <?php echo $this->form->renderField('item_id'); ?>
            <?php echo $this->form->renderField('item_type'); ?>
            <?php echo $this->form->renderField('id'); ?>

            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>