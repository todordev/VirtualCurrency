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
<form action="<?php echo JRoute::_('index.php?option=com_virtualcurrency'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

    <div class="span6 form-horizontal">

        <?php echo JHtml::_('bootstrap.startTabSet', 'currencyTab', array('active' => 'details')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'currencyTab', 'details', JText::_('COM_VIRTUALCURRENCY_DETAILS')); ?>
        <div class="row-fluid">

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('code'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('code'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('symbol'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('symbol'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'currencyTab', 'options', JText::_('COM_VIRTUALCURRENCY_OPTIONS')); ?>
        <div class="row-fluid">
            <div class="span6">
                <?php foreach ($this->form->getFieldset("basic") as $field) { ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $field->label; ?></div>
                        <div class="controls"><?php echo $field->input; ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value=""/>
    <?php echo JHtml::_('form.token'); ?>
</form>