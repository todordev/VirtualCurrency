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
    <div class="vctransactions<?php echo $this->pageclass_sfx; ?>">
        <?php if ($this->params->get('show_page_heading', 1)) { ?>
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        <?php } ?>

        <table class="table table-striped table-bordered vc-accounts-results">
            <thead><?php echo $this->loadTemplate('head'); ?></thead>
            <tbody><?php echo $this->loadTemplate('body'); ?></tbody>
        </table>

    </div>
    <div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink; ?>