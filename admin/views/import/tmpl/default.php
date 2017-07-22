<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span6 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_virtualcurrency'); ?>" method="post" name="adminForm"
              id="adminForm" class="form-validate" enctype="multipart/form-data">

            <fieldset>
                <legend><?php echo $this->legend; ?></legend>
                <?php echo $this->form->renderField('data'); ?>
                <?php echo $this->form->renderField('reset_id'); ?>
                <?php echo $this->form->renderField('remove_old'); ?>

                <div class="alert alert-info">
                    <i class="icon icon-info"></i>
                    <?php echo JText::sprintf('COM_VIRTUALCURRENCY_DOWNLOAD_REAL_CURRENCY_S', 'https://github.com/ITPrism/currency-list'); ?>
                </div>
            </fieldset>

            <input type="hidden" name="task" value="" id="task"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>