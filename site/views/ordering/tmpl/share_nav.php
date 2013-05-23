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
defined('_JEXEC') or die;

$active = array("rewards" => false, "payment" => false, "share" => false);
switch($this->layout) {
    case "default":
        $active["rewards"] = true;
        break;
    case "payment":
        $active["payment"] = true;
        break;
    case "share":
        $active["share"] = true;
        break;
    
}

?>
<div class="navbar">
    <div class="navbar-inner">
    	<a class="brand" href="#"><?php echo JText::_("COM_USERIDEAS_INVESTMENT_PROCESS");?></a>

    	<ul class="nav">
            <li <?php echo ($active["rewards"]) ? 'class="active"' : '';?>>
            	<a href="<?php echo JRoute::_(VirtualCurrencyHelperRoute::getBackingRoute($this->item->slug, $this->item->catslug));?>">
            	(1) <?php echo JText::_("COM_USERIDEAS_STEP_PLEDGE_REWARDS");?>
            	</a>
            </li>
            
            <li <?php echo ($active["payment"]) ? 'class="active"' : '';?>>
            	<?php if(!empty($this->flagStep1)){?> 
                <a href="<?php echo JRoute::_(VirtualCurrencyHelperRoute::getBackingRoute($this->item->slug, $this->item->catslug)."&layout=payment");?>">
                (2) <?php echo JText::_("COM_USERIDEAS_STEP_PAY");?>
                </a>
                <?php } else {?>
                <a href="javascript: void(0);" class="disabled">(2) <?php echo JText::_("COM_USERIDEAS_STEP_PAY");?></a>
                <?php }?>
            </li>
            
            <li <?php echo ($active["share"]) ? 'class="active"' : '';?>>
            	<?php if(!empty($this->flagStep2)){?> 
                <a href="<?php echo JRoute::_(VirtualCurrencyHelperRoute::getBackingRoute($this->item->slug, $this->item->catslug)."&layout=share");?>">
                (3) <?php echo JText::_("COM_USERIDEAS_STEP_SHARE");?>
                </a>
                <?php } else {?>
                <a href="javascript: void(0);" class="disabled">(3) <?php echo JText::_("COM_USERIDEAS_STEP_SHARE");?></a>
                <?php }?>
            </li>
            
        </ul>
     </div>
</div>
