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

<div class="cfbacking-share<?php echo $this->params->get("pageclass_sfx"); ?>">
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
			<h2><?php echo JText::_("COM_USERIDEAS_THANK_YOU_VERY_MUCH");?></h2>
			<p class="message"><?php echo JText::_("COM_USERIDEAS_SUCCESSFULL_INVESTMENT");?></p>
			<h3><?php echo JText::_("COM_USERIDEAS_INVESTMENT_SUMMARY");?></h3>
			<div class="bs-docs-example">
				<p><?php 
				$amount = JHtml::_("crowdfunding.amount", $this->amount, $this->currency);
				echo JText::sprintf("COM_USERIDEAS_INVESTMENT_AMOUNT", $amount); ?></p>
				<p><?php echo JText::sprintf("COM_USERIDEAS_FUNDING_TYPE", $this->item->funding_type);?></p>
				<p class="sticky"><?php
				$goal    = JHtml::_("crowdfunding.amount", $this->item->goal, $this->currency); 
				$endDate = JHtml::_('date', $this->item->funding_end, JText::_('DATE_FORMAT_LC3'));
            	if($this->item->funding_type == "FIXED") {
            	    echo JText::sprintf("COM_USERIDEAS_FUNDING_TYPE_INFO_FIXED", $goal, $endDate);
            	} else {
            	    echo JText::sprintf("COM_USERIDEAS_FUNDING_TYPE_INFO_FLEXIBLE", $endDate);
            	}
				?></p>
			</div>
			
			<h3><?php echo JText::_("COM_USERIDEAS_SELECTED_REWARD");?></h3>
			<div class="bs-docs-example">
			<?php if(!$this->rewardId) {?>
				<p><?php echo JText::_("COM_USERIDEAS_NO_SELECTED_REWARD");?></p>
			<?php } else { ?>
				<h4><?php echo $this->escape($this->reward->title);?></h4>
				<p><?php echo $this->escape($this->reward->description);?></p>
			<?php } ?>
			</div>
			
			<?php echo $this->item->event->afterDisplayContent; ?>
			
    	</div>
    	
    	<div class="span4">
    		<?php echo $this->loadTemplate("info");?>
    		<div class="clearfix">&nbsp;</div>
    	</div>
	</div>
</div>
<div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink;?>