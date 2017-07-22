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
        <form action="<?php echo JRoute::_('index.php?option=com_virtualcurrency'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

            <fieldset class="adminform">
                <?php echo $this->form->renderField('user_id'); ?>
                <?php echo $this->form->renderField('amount'); ?>
                <?php echo $this->form->renderField('currency_id'); ?>
                <?php echo $this->form->renderField('note'); ?>
                <?php echo $this->form->renderField('id'); ?>
            </fieldset>

            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>

        </form>
    </div>
</div>
