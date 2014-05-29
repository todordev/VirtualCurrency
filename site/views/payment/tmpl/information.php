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

    <div class="cfbacking-share<?php echo $this->params->get("pageclass_sfx"); ?>">
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
                <h2><?php echo JText::_("COM_VIRTUALCURRENCY_THANK_YOU_VERY_MUCH"); ?></h2>

                <p class="message"><?php echo JText::_("COM_VIRTUALCURRENCY_SUCCESSFULL_ORDER"); ?></p>

                <h3><?php echo JText::_("COM_VIRTUALCURRENCY_ORDER_SUMMARY"); ?></h3>

                <div class="bs-docs-example">
                    <p><span class="vc-otitle"><?php echo JText::_("COM_VIRTUALCURRENCY_YOU_BOUGHT"); ?></span>
                        <?php
                        echo $this->item->getAmountString($this->amount) . ", (" . $this->escape($this->item->getTitle()) . ")";
                        ?>
                    </p>

                    <p><span class="vc-otitle"><?php echo JText::_("COM_VIRTUALCURRENCY_YOU_PAID"); ?></span>
                        <?php echo $this->realCurrency->getAmountString($this->total); ?>
                    </p>
                </div>

            </div>
        </div>
    </div>
    <div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink;?>