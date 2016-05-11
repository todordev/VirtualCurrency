<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $item) {?>
    <tr>
        <td>
            <?php echo $this->escape($item->title); ?>
        </td>
        <td>
            <?php echo $item->amount; ?>
        </td>
    </tr>
<?php } ?>
