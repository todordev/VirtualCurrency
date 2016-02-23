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
<div class="row-fluid">

    <div class="span12 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_virtualcurrency'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

            <?php echo JHtml::_('bootstrap.startTabSet', 'commodityTab', array('active' => 'details')); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'commodityTab', 'details', JText::_('COM_VIRTUALCURRENCY_DETAILS')); ?>

            <div class="span8">
                <?php echo $this->form->renderField('title'); ?>
                <?php echo $this->form->renderField('icon'); ?>
                <?php echo $this->form->renderField('image'); ?>
                <?php echo $this->form->renderField('published'); ?>
                <?php echo $this->form->renderField('description'); ?>
                <?php echo $this->form->renderField('id'); ?>
            </div>

            <div class="span4">
                <?php if ($this->item->image or $this->item->image_icon) {?>
                    <table class="table">
                        <thead>
                        <tr>
                            <?php if ($this->item->image) {?>
                                <th><?php echo JText::_('COM_VIRTUALCURRENCY_IMAGE');?></th>
                            <?php } ?>
                            <?php if ($this->item->image_icon) {?>
                                <th><?php echo JText::_('COM_VIRTUALCURRENCY_ICON');?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <?php if ($this->item->image) {?>
                                <td><img src="<?php echo $this->mediaFolder .'/'. $this->item->image;?>" /></td>
                            <?php } ?>
                            <?php if ($this->item->image_icon) {?>
                                <td><img src="<?php echo $this->mediaFolder .'/'. $this->item->image_icon;?>" /></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <?php if ($this->item->image) {?>
                                <td>
                                    <a href="<?php echo JRoute::_('index.php?option=com_virtualcurrency&task=commodity.removeImage&type=image&'.JSession::getFormToken().'=1&id='.(int)$this->item->id);?>" class="btn btn-mini btn-danger js-remove-images">
                                        <i class="icon-trash"></i>
                                        <?php echo JText::_('COM_VIRTUALCURRENCY_REMOVE_IMAGE'); ?>
                                    </a>
                                </td>
                            <?php } ?>
                            <?php if ($this->item->image_icon) {?>
                                <td>
                                    <a href="<?php echo JRoute::_('index.php?option=com_virtualcurrency&task=commodity.removeImage&type=icon&'.JSession::getFormToken().'=1&id='.(int)$this->item->id);?>" class="btn btn-mini btn-danger js-remove-images">
                                        <i class="icon-trash"></i>
                                        <?php echo JText::_('COM_VIRTUALCURRENCY_REMOVE_ICON'); ?>
                                    </a>
                                </td>
                            <?php } ?>
                        </tr>
                        </tbody>
                    </table>
                <?php }?>
            </div>

            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'commodityTab', 'options', JText::_('COM_VIRTUALCURRENCY_COMMERCIAL_OPTIONS')); ?>

            <?php echo $this->form->renderField('price'); ?>

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('price_virtual'); ?></div>
                <div class="controls">
                    <?php echo $this->form->getInput('price_virtual'); ?>
                    <?php echo $this->form->getInput('currency_id'); ?>
                </div>
            </div>

            <?php echo $this->form->renderField('number'); ?>
            <?php echo $this->form->renderField('minimum'); ?>

            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.endTabSet'); ?>
            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>