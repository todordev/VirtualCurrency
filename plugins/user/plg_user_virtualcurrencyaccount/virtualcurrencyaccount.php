<?php
/**
 * @package      Virtual Currency
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Virtualcurrency.init');

use Virtualcurrency\Account\Command\Gateway\JoomlaCreateAccounts;
use Virtualcurrency\Account\Command\Gateway\JoomlaUpdateAmount;
use Virtualcurrency\Commodity\Command\Gateway\JoomlaCreateCommodities;

/**
 * This class provides functionality
 * for creating accounts used for storing
 * and managing virtual currency.
 *
 * @package        Virtual Currency
 * @subpackage     Plugins
 */
class plgUserVirtualcurrencyAccount extends JPlugin
{
    /**
     * Method is called after user data is stored in the database
     *
     * @param    array   $user    Holds the new user data.
     * @param    boolean $isNew   True if a new user is stored.
     * @param    boolean $success True if user was succesfully stored in the database.
     * @param    string  $msg     Message.
     *
     * @return    void
     * @since    1.6
     * @throws    Exception on error.
     */
    public function onUserAfterSave($user, $isNew, $success, $msg)
    {
        if ($isNew and JComponentHelper::isEnabled('com_virtualcurrency')) {
            $userId = Joomla\Utilities\ArrayHelper::getValue($user, 'id');

            // Create accounts for users.
            $createAccountsCommand = new Virtualcurrency\Account\Command\CreateAccounts($userId);
            $createAccountsCommand->setGateway(new JoomlaCreateAccounts(JFactory::getDbo()))->handle();

            $createCommoditiesCommand = new Virtualcurrency\Commodity\Command\CreateCommodities($userId);
            $createCommoditiesCommand->setGateway(new JoomlaCreateCommodities(JFactory::getDbo()))->handle();

            if ((bool)$this->params->get('give_units', 0)) {
                $this->giveUnits($userId);
            }
        }
    }

    /**
     *
     * Method is called after user log in to the system.
     *
     * @param    array $user    An associative array of JAuthenticateResponse type.
     * @param    array $options An associative array containing these keys: ["remember"] => bool, ["return"] => string, ["entry_url"] => string.
     *
     * @return    void
     * @since     1.6
     * @throws    Exception on error.
     */
    public function onUserLogin($user, $options)
    {
        if ((bool)$this->params->get('give_units', 0) and JComponentHelper::isEnabled('com_virtualcurrency')) {
            $username = Joomla\Utilities\ArrayHelper::getValue($user, 'username');

            $user   = JFactory::getUser($username);
            $userId = (int)$user->get('id');

            // Used only for testing.
            if ($user->authorise('core.admin')) {
                $this->giveUnits($userId);
            }
        }
    }

    /**
     *
     * Add virtual currency to user account after registration.
     *
     * @param int $userId
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    protected function giveUnits($userId)
    {
        $units      = (int)$this->params->get('number', 0);
        $currencyId = (int)$this->params->get('unit', 0);

        if ($units > 0 and $currencyId > 0) {
            $mapper     = new Virtualcurrency\Currency\Mapper(new \Virtualcurrency\Currency\Gateway\JoomlaGateway(JFactory::getDbo()));
            $repository = new Virtualcurrency\Currency\Repository($mapper);
            $currency   = $repository->fetchById($currencyId);

            if ($currency->getId()) {
                $conditions = array(
                    'user_id'     => $userId,
                    'currency_id' => $currency->getId(),
                );

                // Add the units to the account
                $mapper     = new Virtualcurrency\Account\Mapper(new Virtualcurrency\Account\Gateway\JoomlaGateway(JFactory::getDbo()));
                $repository = new Virtualcurrency\Account\Repository($mapper);
                $account    = $repository->fetch($conditions);

                if ($account->getId()) {
                    $this->loadLanguage();

                    $account->increaseAmount($units);

                    $updateAmountCommand = new Virtualcurrency\Account\Command\UpdateAmount($account);
                    $updateAmountCommand->setGateway(new JoomlaUpdateAmount(JFactory::getDbo()))->handle();

                    // Store transaction
                    $data = array(
                        'title'            => $currency->getTitle(),
                        'units'            => $units,
                        'txn_id'           => strtoupper(Prism\Utilities\StringHelper::generateRandomString(16)),
                        'txn_amount'       => 0,
                        'txn_currency'     => '',
                        'txn_status'       => 'completed',
                        'service_provider' => JText::_('PLG_USER_VIRTUALCURRENCYACCOUNT_SYSTEM'),
                        'service_alias'    => 'system',
                        'item_id'          => $currency->getId(),
                        'item_type'        => 'currency',
                        'sender_id'        => Virtualcurrency\Constants::BANK_ID,
                        'receiver_id'      => $userId
                    );

                    $transaction = new Virtualcurrency\Transaction\Transaction();
                    $transaction->bind($data);

                    $mapper     = new Virtualcurrency\Transaction\Mapper(new Virtualcurrency\Transaction\Gateway\JoomlaGateway(JFactory::getDbo()));
                    $repository = new Virtualcurrency\Transaction\Repository($mapper);
                    $repository->store($transaction);

                    // Integrate with notifier

                    // Notification services
                    $nServices = $this->params->get('integration');
                    if ($nServices) {
                        $message = JText::sprintf('PLG_USER_VIRTUALCURRENCYACCOUNT_NOTIFICATION_AFTER_REGISTRATION', $units, $currency->getTitle());
                        $this->notify($nServices, $message, $userId);
                    }
                }
            }
        }
    }

    public function notify($nServices, $message, $userId)
    {
        $options = new Joomla\Registry\Registry(array(
            'platform' => strtolower($nServices),
            'user_id'  => $userId,
            'title'    => JText::sprintf('PLG_USER_VIRTUALCURRENCYACCOUNT_NOTIFICATION_TITLE')
        ));

        $factory  = new Prism\Integration\Notification\Factory($options);
        $notifier = $factory->create();
        $notifier->setDb(JFactory::getDbo());

        $notifier->send($message);
    }
}
