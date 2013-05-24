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

$active = array("currency" => false, "payment" => false, "information" => false);
switch($this->layout) {
    case "default":
        $active["currency"] = true;
        break;
    case "payment":
        $active["payment"] = true;
        break;
    case "information":
        $active["information"] = true;
        break;
}

?>
<div class="navbar">
    <div class="navbar-inner">
    	<a class="brand" href="<?php echo JRoute::_("index.php?option=com_virtualcurrency&view=ordering", false)?>"><?php echo JText::_("COM_VIRTUALCURRENCY_ORDERING_PROCESS");?></a>

    	<ul class="nav">
            <li <?php echo ($active["currency"]) ? 'class="active"' : '';?>>
            	<a href="<?php echo JRoute::_("index.php");?>">
            	(1) <?php echo JText::_("COM_VIRTUALCURRENCY_CURRENCY");?>
            	</a>
            </li>
            
            <li <?php echo ($active["payment"]) ? 'class="active"' : '';?>>
            	<?php if(!empty($this->flagStep1)){?> 
                <a href="<?php echo JRoute::_("index.php?option=com_virtualcurrency&view=ordering&layout=payment");?>">
                (2) <?php echo JText::_("COM_VIRTUALCURRENCY_PAYMENT");?>
                </a>
                <?php }else {?>
                <a href="javascript: void(0);" class="disabled">(2) <?php echo JText::_("COM_VIRTUALCURRENCY_PAYMENT");?></a>
                <?php }?>
            </li>
            
            <li <?php echo ($active["information"]) ? 'class="active"' : '';?>>
            	<?php if(!empty($this->flagStep2)){?> 
                <a href="<?php echo JRoute::_("index.php?option=com_virtualcurrency&view=buying&layout=share");?>">
                (3) <?php echo JText::_("COM_VIRTUALCURRENCY_INFORMATION");?>
                </a>
                <?php }else {?>
                <a href="javascript: void(0);" class="disabled">(3) <?php echo JText::_("COM_VIRTUALCURRENCY_INFORMATION");?></a>
                <?php }?>
            </li>
            
        </ul>
     </div>
</div>
