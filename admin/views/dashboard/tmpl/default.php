<?php
/**
 * @package     Virtual Currency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Virtual Currency is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
// no direct access
defined('_JEXEC') or die;
?>
<?php if(!empty( $this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php else : ?>
<div id="j-main-container">
<?php endif;?>

    <div class="span8">
        
	</div>
	<div class="span4">
        <a href="http://itprism.com/free-joomla-extensions/ecommerce-gamification/virtual-currency-accounts-manager" target="_blank"><img src="../media/com_virtualcurrency/images/logo.png" alt="<?php echo JText::_("COM_VIRTUALCURRENCY");?>" /></a>
        <a href="http://itprism.com" target="_blank" ><img src="../media/com_virtualcurrency/images/product_of_itprism.png" alt="<?php echo JText::_("COM_VIRTUALCURRENCY_PRODUCT");?>" /></a>
        <p><?php echo JText::_("COM_VIRTUALCURRENCY_YOUR_VOTE"); ?></p>
        <p><?php echo JText::_("COM_VIRTUALCURRENCY_SPONSORSHIP"); ?></p>
        <p><?php echo JText::_("COM_VIRTUALCURRENCY_SUBSCRIPTION"); ?></p>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td><?php echo JText::_("COM_VIRTUALCURRENCY_INSTALLED_VERSION");?></td>
                    <td><?php echo $this->version->getMediumVersion();?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_VIRTUALCURRENCY_RELEASE_DATE");?></td>
                    <td><?php echo $this->version->releaseDate?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_VIRTUALCURRENCY_ITPRISM_LIBRARY_VERSION");?></td>
                    <td><?php echo $this->itprismVersion;?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_VIRTUALCURRENCY_COPYRIGHT");?></td>
                    <td><?php echo $this->version->copyright;?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_VIRTUALCURRENCY_LICENSE");?></td>
                    <td><?php echo $this->version->license;?></td>
                </tr>
            </tbody>
        </table>
	</div>
	
</div>