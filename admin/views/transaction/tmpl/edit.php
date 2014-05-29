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
<div class="row-fluid">
    <div class="span6 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_virtualcurrency'); ?>" method="post" name="adminForm"
              id="adminForm" class="form-validate">
            <fieldset class="adminform">

                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('currency_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('currency_id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('units'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('units'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('txn_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('txn_id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('txn_amount'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('txn_amount'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('txn_currency'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('txn_currency'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('txn_status'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('txn_status'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('txn_date'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('txn_date'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('service_provider'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('service_provider'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('sender_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('sender_id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('receiver_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('receiver_id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                </div>

            </fieldset>

            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>

        </form>
    </div>
</div>
