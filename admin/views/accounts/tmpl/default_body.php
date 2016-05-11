<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {
    $currency  = $this->virtualCurrencies->getCurrency($item->currency_id);
    $this->money->setCurrency($currency);
    ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'accounts.'); ?>
        </td>
        <td class="has-context">
            <a href="<?php echo JRoute::_('index.php?option=com_virtualcurrency&view=account&layout=edit&id=' . (int)$item->id); ?>"><?php echo $this->escape($item->name); ?></a>
            <div class="small">
                <div>
                    <?php echo JText::_('COM_VIRTUALCURRENCY_ACCOUNT_CURRENCY'); ?> :
                    <a href="<?php echo JRoute::_('index.php?option=com_virtualcurrency&view=currencies&filter_search=id:' . (int)$item->currency_id); ?>"><?php echo $this->escape($item->currency_title); ?>
                        [ <?php echo $this->escape($item->currency_code); ?> ]
                    </a>
                </div>
            </div>
        </td>
        <td class="hidden-phone">
            <?php echo $this->money->setAmount($item->amount)->formatCurrency(); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->created_at; ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>
