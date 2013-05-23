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
	
	<?php foreach($this->currencies as $currency) {?>
	<div class="row-fluid">
		<div class="span8">
			
			<div class="bs-docs-example">
    			<form method="post" action="<?php echo JRoute::_("index.php")?>" id="form-payment" autocomplete="off">
    			
    				<h3><?php echo JText::sprintf("COM_VIRTUALCURRENCY_BUY_CURRENCY", $this->escape($currency["title"]));?></h3>
    				<label><?php echo JText::sprintf("COM_VIRTUALCURRENCY_NUMBER_OF_CURRENCY", $this->escape($currency["title"]));?></label>
    				<?php echo JHtml::_("virtualcurrency.inputAmount", $this->currencyAmount, $currency, array("name"=>"amount")); ?>
    				
    				<span class="help-block">
    				    <?php echo JText::sprintf("COM_VIRTUALCURRENCY_HELP_MIN_AMOUNT", $this->escape($currency["title"]), $currency["minimum"]);?>
    			    </span>
    				
    				<input type="hidden" name="id" value="<?php echo $currency["id"]; ?>" />
    				<input type="hidden" name="task" value="ordering.step1" />
    				<?php echo JHtml::_('form.token'); ?> 
    				
    				<?php if($this->params->get("ordering_service_terms", 0)) {?>
    	                <input type="hidden" name="terms" value="0" class="vc-terms" />
                    <?php }?>
    				
    				<div class="clearfix"></div>
    				<button type="submit" class="button" <?php echo $this->disabledButton;?>><?php echo JText::_("COM_VIRTUALCURRENCY_CONTINUE");?></button>
        				
                </form>
            </div>
            
    	</div>
    	
	</div>
	<?php }?>
	
	<?php 
	if($this->params->get("ordering_service_terms", 0)) {
	    $termsUrl = $this->params->get("ordering_service_terms_url", "");
	?>
	<label class="checkbox">
    	<input type="checkbox" name="terms" value="1" id="vc-terms" autocomplete="off"> <?php echo (!$termsUrl) ? JText::_("COM_VIRTUALCURRENCY_TERMS_AGREEMENT") : JText::sprintf("COM_VIRTUALCURRENCY_TERMS_AGREEMENT_URL", $termsUrl);?>
    </label>
    <?php }?>
    
</div>
<div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink;?>