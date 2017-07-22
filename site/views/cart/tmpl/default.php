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

    <?php
    /** @var \Virtualcurrency\Currency\Currency $currency */
    foreach ($this->currencies as $currency) {
        if (!$currency->getParam('price_real') and !$currency->getParam('price_virtual')) {
            continue;
        }
        $id = 'vc-currency-id-'.$currency->getId();
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo JHtml::_('virtualcurrency.displayTitle', $currency, $this->params, $this->imageFolder); ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo JRoute::_('index.php'); ?>" method="post" >
                    <div class="form-group">
                        <label for="<?php echo $id; ?>"><?php echo JText::sprintf('COM_VIRTUALCURRENCY_NUMBER_OF_S', $this->escape($currency->getTitle())); ?></label>
                        <?php echo JHtml::_('virtualcurrency.inputAmount', $currency, array('name' => 'amount', 'class' => 'form-control', 'id' => $id)); ?>

                        <?php if ((int)$currency->getParam('minimum') > 0) {?>
                        <span class="help-block">
                            <?php echo JText::sprintf('COM_VIRTUALCURRENCY_HELP_MIN_AMOUNT', $this->escape($currency->getTitle()), $currency->getParam('minimum')); ?>
                        </span>
                        <?php } ?>

                        <input type="hidden" name="id" value="<?php echo $currency->getId(); ?>" />
                        <input type="hidden" name="type" value="currency" />
                        <input type="hidden" name="task" value="cart.process" />
                        <?php echo JHtml::_('form.token'); ?>

                        <?php
                        if ($this->params->get('payments_service_terms', 0)) {
                            $termsUrl = $this->params->get('payments_service_terms_url', '');
                            ?>
                            <div class="checkbox">
                                <label >
                                    <input type="checkbox" name="terms" value="1" id="vc-terms" autocomplete="off" /> <?php echo (!$termsUrl) ? JText::_('COM_VIRTUALCURRENCY_TERMS_AGREEMENT') : JText::sprintf('COM_VIRTUALCURRENCY_TERMS_AGREEMENT_URL', $termsUrl); ?>
                                </label>
                            </div>
                        <?php } ?>

                        <button type="submit" class="btn mt-10" <?php echo $this->disabledButton; ?>>
                            <?php echo JText::_('COM_VIRTUALCURRENCY_SUBMIT_CONTINUE'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>

    <?php
    /** @var \Virtualcurrency\Commodity\Commodity $commodity */
    foreach ($this->commodities as $commodity) {
        if ((!$commodity->getParam('price_real') and !$commodity->getParam('price_virtual')) or !$commodity->hasUnits()) {
            continue;
        }
        $id = 'vc-commodity-id-'.$commodity->getId();
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo JHtml::_('virtualcurrency.displayTitle', $commodity, $this->params, $this->imageFolder); ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo JRoute::_('index.php'); ?>" method="post" >
                    <div class="form-group">
                        <label for="<?php echo $id; ?>"><?php echo JText::sprintf('COM_VIRTUALCURRENCY_NUMBER_OF_S', $this->escape($commodity->getTitle())); ?></label>
                        <input type="text" value="<?php echo $commodity->getMinimum();?>" name="amount" class="form-control" id="<?php echo $id; ?>" />

                        <?php if ($commodity->getInStock() !== null and (int)$commodity->getInStock() > 0) {?>
                        <span class="help-block">
                            <?php
                            if ((int)$commodity->getInStock() === 1) {
                                echo JText::sprintf('COM_VIRTUALCURRENCY_HELP_IN_STOCK_1', $commodity->getInStock(), $this->escape($commodity->getTitle()));
                            } else {
                                echo JText::sprintf('COM_VIRTUALCURRENCY_HELP_IN_STOCK_MORE', $commodity->getInStock(), $this->escape($commodity->getTitle()));
                            }
                            ?>
                        </span>
                        <?php } ?>

                        <?php if ((int)$commodity->getMinimum() > 0) {?>
                        <span class="help-block">
                            <?php echo JText::sprintf('COM_VIRTUALCURRENCY_HELP_MIN_AMOUNT', $this->escape($commodity->getTitle()), $commodity->getMinimum()); ?>
                        </span>
                        <?php } ?>

                        <input type="hidden" name="id" value="<?php echo $commodity->getId(); ?>" />
                        <input type="hidden" name="type" value="commodity" />
                        <input type="hidden" name="task" value="cart.process" />
                        <?php echo JHtml::_('form.token'); ?>

                        <?php
                        if ($this->params->get('payments_service_terms', 0)) {
                            $termsUrl = $this->params->get('payments_service_terms_url', '');
                            ?>
                        <div class="checkbox">
                            <label >
                                <input type="checkbox" name="terms" value="1" id="vc-terms" autocomplete="off" /> <?php echo (!$termsUrl) ? JText::_('COM_VIRTUALCURRENCY_TERMS_AGREEMENT') : JText::sprintf('COM_VIRTUALCURRENCY_TERMS_AGREEMENT_URL', $termsUrl); ?>
                            </label>
                        </div>
                        <?php } ?>

                        <button type="submit" class="btn mt-10" <?php echo $this->disabledButton; ?>>
                            <?php echo JText::_('COM_VIRTUALCURRENCY_SUBMIT_CONTINUE'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>
</div>