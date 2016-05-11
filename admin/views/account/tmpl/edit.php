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

if ($this->item->id !== null and $this->item->id > 0) {
    $field = $this->form->setFieldAttribute('currency_id', 'disabled', '1');
}
?>
<div class="row-fluid">
    <div class="span6 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_virtualcurrency'); ?>" method="post" name="adminForm"
              id="adminForm" class="form-validate">
            <fieldset class="adminform">

                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('amount'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('amount'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('currency_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('currency_id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('note'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('note'); ?></div>
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
