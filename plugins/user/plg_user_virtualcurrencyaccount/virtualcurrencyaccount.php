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

/**
 * This class provides functionality
 * for creating accounts used for storing
 * and managing virtual currency.
 *
 * @package        Virtual Currency
 * @subpackage     Plugins
 */
class plgUserVirtualCurrencyAccount extends JPlugin
{
    /**
     *
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
            VirtualCurrencyHelper::createAccounts($userId);
            VirtualCurrencyHelper::createCommodities($userId);

            if ($this->params->get('give_units', 0)) {
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
     * @since    1.6
     * @throws    Exception on error.
     */
    public function onUserLogin($user, $options)
    {
        if (!JComponentHelper::isEnabled('com_virtualcurrency')) {
            return;
        }

        // Get user id
        $userName = Joomla\Utilities\ArrayHelper::getValue($user, 'username');

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id')
            ->from($db->quoteName('#__users', 'a'))
            ->where('a.username = ' . $db->quote($userName));

        $db->setQuery($query, 0, 1);
        $userId = (int)$db->loadResult();

        // Create accounts for users.
        VirtualCurrencyHelper::createAccounts($userId);
        VirtualCurrencyHelper::createCommodities($userId);

        // Used only for testing.
//        $this->giveUnits($userId);
    }
    

    /**
     *
     * Add virtual currency to user account after registration.
     *
     * @param integer $userId
     */
    protected function giveUnits($userId)
    {
        $this->loadLanguage();

        $units      = (int)$this->params->get('give_units_number', 0);
        $currencyId = (int)$this->params->get('give_units_unit', 0);

        if ($units > 0 and $currencyId > 0) {

            $currency = Virtualcurrency\Currency\Currency::getInstance(JFactory::getDbo(), $currencyId);

            if ($currency->getId()) {

                $keys = array(
                    'user_id' => $userId,
                    'currency_id' => $currency->getId(),
                );

                // Add the units to the account
                $account = new Virtualcurrency\Account\Account(JFactory::getDbo());
                $account->load($keys);
                $account->increaseAmount($units);
                $account->storeAmount();

                // Store transaction
                $transaction = new Virtualcurrency\Transaction\Transaction(JFactory::getDbo());

                $txnId = JString::strtoupper(Prism\Utilities\StringHelper::generateRandomString(16));

                $data = array(
                    'title'            => $currency->getTitle(),
                    'units'            => $units,
                    'txn_id'           => $txnId,
                    'txn_amount'       => 0,
                    'txn_currency'     => $currency->getCode(),
                    'txn_status'       => 'completed',
                    'service_provider' => JText::_('PLG_USER_VIRTUALCURRENCYACCOUNT_SYSTEM'),
                    'item_id'          => $currency->getId(),
                    'item_type'        => 'currency',
                    'sender_id'        => Virtualcurrency\Constants::BANK_ID,
                    'receiver_id'      => $userId
                );

                $transaction->bind($data);
                $transaction->store();
            }

            // Integrate with notifier

            // Notification services
            $nServices = $this->params->get('give_units_integrate');
            if ($nServices) {
                $message = JText::sprintf('PLG_USER_VIRTUALCURRENCYACCOUNT_NOTIFICATION_AFTER_REGISTRATION', $units, $currency->getTitle());
                $this->notify($nServices, $message, $userId);
            }

        }

    }

    public function notify($nServices, $message, $userId)
    {

        switch ($nServices) {

            case 'gamification':
                $notifier = new Prism\Integration\Notification\Gamification($userId, $message);
                $notifier->send();
                break;

            case 'socialcommunity':

                $notifier = new Prism\Integration\Notification\Socialcommunity($userId, $message);
                $notifier->send();
                break;

            default:

                break;

        }

    }
}
