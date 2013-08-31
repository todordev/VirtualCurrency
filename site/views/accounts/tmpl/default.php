<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
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
<div class="vctransactions<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
	<table class="table table-striped table-bordered vc-accounts-results">
       <thead><?php echo $this->loadTemplate('head');?></thead>
	   <tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
        
</div>
<div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink;?>