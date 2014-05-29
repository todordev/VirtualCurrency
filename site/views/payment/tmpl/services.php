<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>

    <div class="vcbuying<?php echo $this->params->get("pageclass_sfx"); ?>">
        <?php if ($this->params->get('show_page_heading', 1)) : ?>
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        <?php endif; ?>

        <div class="row-fluid">
            <div class="span12">
                <?php
                $layout = new JLayoutFile('wizard', $this->layoutsBasePath);
                echo $layout->render($this->layoutData);
                ?>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <h2><?php echo JText::_("COM_VIRTUALCURRENCY_ORDER_SUMMARY"); ?></h2>

                <div class="bs-docs-example">
                    <p><span class="vc-otitle"><?php echo JText::_("COM_VIRTUALCURRENCY_YOU_ARE_BUYING"); ?></span>
                        <?php
                        echo $this->item->getAmountString($this->amount) . ", (" . $this->escape($this->item->getTitle()) . ")";
                        ?>
                    </p>

                    <p><span class="vc-otitle"><?php echo JText::_("COM_VIRTUALCURRENCY_YOU_WILL_PAY"); ?></span>
                        <?php echo $this->realCurrency->getAmountString($this->total); ?>
                    </p>
                </div>


                <h2><?php echo JText::_("COM_VIRTUALCURRENCY_PAYMENT_METHODS"); ?></h2>

                <div class="bs-docs-example">
                    <?php echo $this->item->event->onProjectPayment; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink;?>