<?php
/**
 * @package      Virtual Currency
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */
 
// no direct access
defined('_JEXEC') or die;
/**
 * @var $accounts \Virtualcurrency\Account\Accounts
 * @var $account \Virtualcurrency\Account\Account
 * @var $currencies \Virtualcurrency\Currency\Currencies
 * @var $currency \Virtualcurrency\Currency\Currency
 * @var $formatter \Prism\Money\Formatter\IntlDecimalFormatter
 */
?>
<?php
foreach ($accounts as $account) {
    $virtualCurrency = $currencies->fetchById($account->getCurrencyId());
    $currency = new \Prism\Money\Currency($virtualCurrency->getProperties());

    if ($currency !== null) {
        $money = new \Prism\Money\Money($account->getAmount(), $currency);
        ?>
        <p class="vcm-account-amount">
            <?php echo htmlentities($account->getCurrency()->getTitle(), ENT_QUOTES, 'UTF-8'); ?>: <?php echo $formatter->formatCurrency($money); ?>
        </p>
        <?php
    }
}
