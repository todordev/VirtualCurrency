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

<div class="vccart<?php echo $this->params->get('pageclass_sfx'); ?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <?php
            $layout = new JLayoutFile('wizard');
            echo $layout->render($this->layoutData);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2><?php echo JText::_('COM_VIRTUALCURRENCY_THANK_YOU_VERY_MUCH'); ?></h2>
            <p class="message"><?php echo JText::_('COM_VIRTUALCURRENCY_SUCCESSFULL_ORDER'); ?></p>

            <?php if ($this->item) {?>
            <h3><?php echo JText::_('COM_VIRTUALCURRENCY_ORDER_SUMMARY'); ?></h3>
            <p>
                <?php echo JText::sprintf('COM_VIRTUALCURRENCY_YOU_BOUGHT_S', $this->item->order->getItemsNumberFormatted()); ?>
            </p>
            <?php } ?>
        </div>
    </div>
</div>
