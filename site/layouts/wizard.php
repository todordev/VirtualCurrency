<?php
/**
 * @package      Virtual Currency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$active = array('currency' => false, 'payment' => false, 'summary' => false);

switch ($displayData->layout) {
    case 'default':
        $active['currency'] = true;
        break;
    case 'payment':
        $active['payment'] = true;
        break;
    case 'summary':
        $active['summary'] = true;
        break;
}
?>
<div class="navbar navbar-default vc-payment-wizard" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="javascript:void(0);"><?php echo JText::_('COM_VIRTUALCURRENCY_PAYMENT_PROCESS'); ?></a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li <?php echo ($active['currency']) ? 'class="active"' : ''; ?>>
                    <a href="<?php echo JRoute::_(VirtualcurrencyHelperRoute::getCartRoute()); ?>">
                        (1) <?php echo JText::_('COM_VIRTUALCURRENCY_CART'); ?>
                    </a>
                </li>

                <li <?php echo ($active['payment']) ? 'class="active"' : ''; ?>>
                    <?php if ((bool)$displayData->cartSession->step1 === true) { ?>
                        <a href="<?php echo JRoute::_(VirtualcurrencyHelperRoute::getCartRoute('payment')); ?>">
                            (2) <?php echo JText::_('COM_VIRTUALCURRENCY_PAYMENT'); ?>
                        </a>
                    <?php } else { ?>
                        <a href="javascript: void(0);"
                           class="disabled">(2) <?php echo JText::_('COM_VIRTUALCURRENCY_PAYMENT'); ?></a>
                    <?php } ?>
                </li>

                <li <?php echo ($active['summary']) ? 'class="active"' : ''; ?>>
                    <?php if ((isset($displayData->cartSession->step2) and (bool)$displayData->cartSession->step2 === true)) { ?>
                        <a href="<?php echo JRoute::_(VirtualcurrencyHelperRoute::getCartRoute('summary')); ?>">
                            (3) <?php echo JText::_('COM_VIRTUALCURRENCY_SUMMARY'); ?>
                        </a>
                    <?php } else { ?>
                        <a href="javascript: void(0);"
                           class="disabled">(3) <?php echo JText::_('COM_VIRTUALCURRENCY_SUMMARY'); ?></a>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </div>
</div>
