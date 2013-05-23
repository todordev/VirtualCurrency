<?php
/**
 * @package      ITPrism Components
 * @subpackage   VirtualCurrency
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VirtualCurrency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;?>

<div class="vcbuying<?php echo $this->params->get("pageclass_sfx"); ?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>
	
	<div class="row-fluid">
		<div class="span12">
    		<?php echo $this->loadTemplate("nav");?>	
    	</div>
	</div>
	
	<div class="row-fluid">
		<div class="span8">
			<h2><?php echo JText::_("COM_VIRTUALCURRENCY_ORDER_SUMMARY");?></h2>
			<div class="bs-docs-example">
				<p><span class="vc-otitle"><?php echo JText::_("COM_VIRTUALCURRENCY_YOU_ARE_BUYING"); ?></span>
				<?php 
				$amount = JHtml::_("virtualcurrency.amount", $this->amount, $this->currency);
				echo $amount.", (".$this->escape($this->item->title).")";
				?>
				</p>
				<p><span class="vc-otitle"><?php echo JText::_("COM_VIRTUALCURRENCY_YOU_WILL_PAY"); ?></span>
				<?php echo $this->total." ".$this->escape($this->item->currency);?>
				</p>
			</div>
			
			
			<h2><?php echo JText::_("COM_VIRTUALCURRENCY_PAYMENT_METHODS");?></h2>
			<div class="bs-docs-example">
				<?php echo $this->item->event->onProjectPayment;?>
			</div>
    	</div>
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink;?>