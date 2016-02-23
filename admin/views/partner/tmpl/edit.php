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
<form action="<?php echo JRoute::_('index.php?option=com_virtualcurrency'); ?>" method="post" name="adminForm"
      id="adminForm" class="form-validate">
    <div class="width-40 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_("COM_VIRTUALCURRENCY_PARTNER_DATA_LEGEND"); ?></legend>

            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('title'); ?>
                    <?php echo $this->form->getInput('title'); ?></li>

                <li><?php echo $this->form->getLabel('website'); ?>
                    <?php echo $this->form->getInput('website'); ?></li>

                <li><?php echo $this->form->getLabel('service_url'); ?>
                    <?php echo $this->form->getInput('service_url'); ?></li>

                <li><?php echo $this->form->getLabel('note'); ?>
                    <?php echo $this->form->getInput('note'); ?></li>

                <li><?php echo $this->form->getLabel('published'); ?>
                    <?php echo $this->form->getInput('published'); ?></li>

                <li><?php echo $this->form->getLabel('id'); ?>
                    <?php echo $this->form->getInput('id'); ?></li>
            </ul>
        </fieldset>
    </div>

    <input type="hidden" name="task" value=""/>
    <?php echo JHtml::_('form.token'); ?>
</form>
