<?php
/**
 * @package      Virtual Currency
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * This class provides functionality
 * for creating accounts used for storing
 * and managing virtual currency.
 *
 * @package        Virtual Currency
 * @subpackage     Plugins
 */
class plgUserVirtualCurrencyNewAccount extends JPlugin
{

    /**
     * @var Joomla\Registry\Registry
     */
    public $params;

    /**
     *
     * Method is called after user data is stored in the database
     *
     * @param    array   $user    Holds the new user data.
     * @param    boolean $isnew   True if a new user is stored.
     * @param    boolean $success True if user was succesfully stored in the database.
     * @param    string  $msg     Message.
     *
     * @return    void
     * @since    1.6
     * @throws    Exception on error.
     */
    public function onUserAfterSave($user, $isnew, $success, $msg)
    {
        if ($isnew) {

            if (!JComponentHelper::isEnabled("com_virtualcurrency")) {
                return;
            }

            $userId = JArrayHelper::getValue($user, 'id');

            // Create accounts
            $this->createAccount($userId);

            if ($this->params->get("give_units", 0)) {
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
        if (!JComponentHelper::isEnabled("com_virtualcurrency")) {
            return;
        }

        // Get user id
        $userName = JArrayHelper::getValue($user, 'username');

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select("a.id")
            ->from($db->quoteName("#__users", "a"))
            ->where("a.username = " . $db->quote($userName));

        $db->setQuery($query, 0, 1);
        $userId = $db->loadResult();

        $this->createAccount($userId);

        /** @todo remove this. I am using it for testing. */
//        $this->giveUnits($userId);
    }

    /**
     * This method checks for existing accounts current currencies.
     * If there is no account for a currency, it creates new one.
     *
     * @param integer $userId
     */
    protected function createAccount($userId)
    {

        // Get Accounts
        jimport('virtualcurrency.accounts');
        $accounts = new VirtualCurrencyAccounts(JFactory::getDbo());
        $accounts->load($userId);

        $accountIds = array();
        foreach ($accounts as $value) {
            $accountIds[] = $value["currency_id"];
        }

        // Get currencies
        jimport('virtualcurrency.currencies');
        $options = array(
            "state" => 1
        );

        $currencies = new VirtualCurrencyCurrencies(JFactory::getDbo());
        $currencies->load($options);

        jimport("virtualcurrency.account");

        // Check and create accounts
        foreach ($currencies as $currency) {
            if (!in_array($currency["id"], $accountIds)) {

                $account              = new VirtualCurrencyAccount(JFactory::getDbo());

                $data = array(
                    "amount" => 0,
                    "currency_id" => $currency["id"],
                    "user_id" => $userId
                );

                $account->bind($data);
                $account->store();
            }
        }
    }

    /**
     *
     * Add virtual currency to user account after registration.
     *
     * @param integer $userId
     */
    protected function giveUnits($userId)
    {
        $units      = (int)$this->params->get("give_units_number", 0);
        $currencyId = $this->params->get("give_units_unit");

        if (!empty($units) and !empty($currencyId)) {

            jimport("virtualcurrency.currency");
            $currency = VirtualCurrencyCurrency::getInstance(JFactory::getDbo(), $currencyId);

            if ($currency->getId()) {

                // Get the id of the sender ( the bank that generates currency )
                $componentParams = JComponentHelper::getParams("com_virtualcurrency");
                /** @var  $componentParams Joomla\Registry\Registry */

                $senderId        = $componentParams->get("payments_bank_id");

                // Get account ID
                JLoader::register(
                    "VirtualCurrencyHelper",
                    JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_virtualcurrency" .
                    DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "virtualcurrency.php"
                );

                $keys = array(
                    "user_id" => $userId,
                    "currency_id" => $currency->getId(),
                );

                // Add the units to the account
                jimport("virtualcurrency.account");
                $account = new VirtualCurrencyAccount(JFactory::getDbo());
                $account->load($keys);
                $account->increaseAmount($units);
                $account->updateAmount();

                // Store transaction
                jimport("virtualcurrency.transaction");
                $transaction = new VirtualCurrencyTransaction(JFactory::getDbo());

                $seed = substr(md5(uniqid(time() * rand(), true)), 0, 16);

                $data = array(
                    "units"            => $units,
                    "txn_id"           => JString::strtoupper("GEN_" . JString::substr(JApplicationHelper::getHash($seed), 0, 16)),
                    "txn_amount"       => 0,
                    "txn_currency"     => $currency->getCode(),
                    "txn_status"       => "completed",
                    "service_provider" => "System",
                    "currency_id"      => $currency->getId(),
                    "sender_id"        => $senderId,
                    "receiver_id"      => $userId
                );

                $transaction->bind($data);
                $transaction->store();
            }

            // Integrate with notifier

            // Notification services
            $nServices = $this->params->get("give_units_integrate");
            if (!empty($nServices)) {
                $message = JText::sprintf("PLG_USER_VIRTUALCURRENCYNEWACCOUNT_NOTIFICATION_AFTER_REGISTRATION", $units, $currency->getTitle());
                $this->notify($nServices, $message, $userId);
            }

        }

    }

    public function notify($nServices, $message, $userId)
    {

        switch ($nServices) {

            case "gamification":

                jimport("itprism.integrate.notification.gamification");
                $notifier = new ITPrismIntegrateNotificationGamification($userId, $message);
                $notifier->send();

                break;

            case "socialcommunity":

                jimport("itprism.integrate.notification.socialcommunity");
                $notifier = new ITPrismIntegrateNotificationSocialCommunity($userId, $message);
                $notifier->send();

                break;

            default:

                break;

        }

    }
}
