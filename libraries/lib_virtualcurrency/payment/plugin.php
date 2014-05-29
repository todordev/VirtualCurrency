<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Plugin
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * VirtualCurrency payment plugin class.
 *
 * @package      VirtualCurrency
 * @subpackage   Plugin
 */
class VirtualCurrencyPaymentPlugin extends JPlugin
{
    protected $paymentService;

    protected $log;
    protected $textPrefix;
    protected $debugType;

    /**
     * This property contains the parameters of the plugin.
     *
     * @var Joomla\Registry\Registry
     */
    public $params;

    protected $autoloadLanguage = true;

    /**
     * Initialize the object.
     *
     * @param object $subject
     * @param array  $config
     */
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);

        // Prepare log object
        $registry = JRegistry::getInstance("com_virtualcurrency");
        /** @var  $registry Joomla\Registry\Registry */

        $fileName  = $registry->get("logger.file");
        $tableName = $registry->get("logger.table");

        // Create log object
        $this->log = new ITPrismLog();

        if (!empty($tableName)) {
            // Set database writer.
            $this->log->addWriter(new ITPrismLogWriterDatabase(JFactory::getDbo(), $tableName));
        }

        // Set file writer.
        if (!empty($fileName)) {
            $app = JFactory::getApplication();
            /** @var $app JApplicationSite */

            $file = JPath::clean($app->get("log_path") . DIRECTORY_SEPARATOR . $fileName);
            $this->log->addWriter(new ITPrismLogWriterFile($file));
        }

    }

    /**
     * Send emails to the administrator and buyer of units.
     *
     * @param object $currency
     * @param object $transaction
     * @param Joomla\Registry\Registry $params
     */
    protected function sendMails($currency, $transaction, $params)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Get website
        $uri     = JUri::getInstance();
        $website = $uri->toString(array("scheme", "host"));

        $emailMode = $this->params->get("email_mode", "plain");

        jimport("virtualcurrency.currency");
        $units   = VirtualCurrencyCurrency::getInstance(JFactory::getDbo(), $currency->id);

        jimport("virtualcurrency.realcurrency");
        $currencyId = $params->get("payments_currency_id");
        $realCurrency   = VirtualCurrencyRealCurrency::getInstance(JFactory::getDbo(), $currencyId, $params);

        // Prepare data for parsing
        $data = array(
            "site_name"      => $app->get("sitename"),
            "site_url"       => JUri::root(),
            "item_title"     => $currency->title,
            "order_url"      => $website . JRoute::_(VirtualCurrencyHelperRoute::getAccountsRoute()),
            "units"          => $units->getAmountString($transaction->units),
            "units_title"    => $units->getTitle(),
            "amount"         => $realCurrency->getAmountString($transaction->txn_amount),
            "transaction_id" => $transaction->txn_id
        );

        // DEBUG DATA
        JDEBUG ? $this->log->add($this->textPrefix . "_DEBUG_MAIL_DATA", $this->debugType, var_export($data, true)) : null;

        // Send mail to the administrator
        $emailId = $this->params->get("admin_mail_id", 0);
        if (!empty($emailId)) {

            jimport("virtualcurrency.email");
            $email = new VirtualCurrencyEmail();
            $email->setDb(JFactory::getDbo());
            $email->load($emailId);

            if (!$email->getSenderName()) {
                $email->setSenderName($app->get("fromname"));
            }
            if (!$email->getSenderEmail()) {
                $email->setSenderEmail($app->get("mailfrom"));
            }

            $recipientName = $email->getSenderName();
            $recipientMail = $email->getSenderEmail();

            // Prepare data for parsing
            $data["sender_name"]     = $email->getSenderName();
            $data["sender_email"]    = $email->getSenderEmail();
            $data["recipient_name"]  = $recipientName;
            $data["recipient_email"] = $recipientMail;

            $email->parse($data);
            $subject = $email->getSubject();
            $body    = $email->getBody($emailMode);

            $mailer = JFactory::getMailer();
            if (strcmp("html", $emailMode) == 0) { // Send as HTML message
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, VirtualCurrencyConstants::MAIL_MODE_HTML);
            } else { // Send as plain text.
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, VirtualCurrencyConstants::MAIL_MODE_PLAIN_TEXT);
            }

            // Check for an error.
            if ($return !== true) {
                $this->log->add(JText::_($this->textPrefix . "_ERROR_MAIL_SENDING_ADMIN"), $this->debugType);
            }

        }

        // Send mail to buyer
        $emailId    = $this->params->get("user_mail_id", 0);
        $userId     = $transaction->receiver_id;
        if (!empty($emailId) and !empty($userId)) {

            $email = new VirtualCurrencyEmail();
            $email->setDb(JFactory::getDbo());
            $email->load($emailId);

            if (!$email->getSenderName()) {
                $email->setSenderName($app->get("fromname"));
            }
            if (!$email->getSenderEmail()) {
                $email->setSenderEmail($app->get("mailfrom"));
            }

            $user          = JFactory::getUser($userId);
            $recipientName = $user->get("name");
            $recipientMail = $user->get("email");

            // Prepare data for parsing
            $data["sender_name"]     = $email->getSenderName();
            $data["sender_email"]    = $email->getSenderEmail();
            $data["recipient_name"]  = $recipientName;
            $data["recipient_email"] = $recipientMail;

            $email->parse($data);
            $subject = $email->getSubject();
            $body    = $email->getBody($emailMode);

            $mailer = JFactory::getMailer();
            if (strcmp("html", $emailMode) == 0) { // Send as HTML message
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, VirtualCurrencyConstants::MAIL_MODE_HTML);

            } else { // Send as plain text.
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, VirtualCurrencyConstants::MAIL_MODE_PLAIN_TEXT);

            }

            // Check for an error.
            if ($return !== true) {

                // Log error
                $this->log->add(
                    JText::_($this->textPrefix . "_ERROR_MAIL_SENDING_USER"),
                    $this->debugType
                );

            }

        }

    }
}
