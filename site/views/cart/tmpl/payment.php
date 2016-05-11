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
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><?php echo JText::_('COM_VIRTUALCURRENCY_ORDER_SUMMARY'); ?></h2>
                </div>
                <div class="panel-body">
                    <div class="bs-docs-example">
                        <p><span class="vc-otitle"><?php echo JText::_('COM_VIRTUALCURRENCY_YOU_ARE_BUYING'); ?></span>
                            <?php
                            echo $this->item->order['items_number_formatted'];
                            ?>
                        </p>

                        <p>
                            <span class="vc-otitle">
                                <?php
                                if ($this->item->order['real']['items_cost_formatted'] and $this->item->order['virtual']['items_cost_formatted']) {
                                    echo JText::sprintf('COM_VIRTUALCURRENCY_YOU_WILL_PAY_S_S', $this->item->order['real']['items_cost_formatted'], $this->item->order['virtual']['items_cost_formatted']);
                                } else {
                                    echo JText::sprintf('COM_VIRTUALCURRENCY_YOU_WILL_PAY_S', ($this->item->order['real']['items_cost_formatted']) ?: $this->item->order['virtual']['items_cost_formatted']);
                                }
                                ?>
                            </span>
                        </p>

                    </div>

                </div>
            </div>

            <?php if (!empty($this->item->event->onPreparePayment)) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><?php echo JText::_('COM_VIRTUALCURRENCY_PAYMENT_METHODS'); ?></h2>
                </div>
                <div class="panel-body">
                    <?php echo $this->item->event->onPreparePayment; ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>