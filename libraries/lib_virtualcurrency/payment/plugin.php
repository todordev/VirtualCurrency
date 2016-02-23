<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Plugin
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Prism;
use Virtualcurrency;
use EmailTemplates;

// no direct access
defined('_JEXEC') or die;

/**
 * Virtualcurrency payment plugin class.
 *
 * @package      Virtualcurrency
 * @subpackage   Payments
 */
class Plugin extends \JPlugin
{
    protected $serviceProvider;
    protected $serviceAlias;

    protected $log;
    protected $textPrefix = 'PLG_VIRTUALCURRENCYPAYMENT';
    protected $debugType  = 'DEBUG_PAYMENT_PLUGIN';

    protected $logFile    = 'com_virtualcurrency.php';

    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * @var \JApplicationSite
     */
    protected $app;

    /**
     * This property contains keys of response data
     * that will be used to be generated an array with extra data.
     *
     * @var array
     */
    protected $extraDataKeys = array();

    public function __construct(&$subject, $config = array())
    {
        // Set file writer.
        if ($this->logFile and $this->logFile !== '') {

            // Create log object
            $this->log = new Prism\Log\Log();

            $app = \JFactory::getApplication();
            /** @var $app \JApplicationSite */

            $file = \JPath::clean($app->get('log_path') . DIRECTORY_SEPARATOR . basename($this->logFile));
            $this->log->addAdapter(new Prism\Log\Adapter\File($file));
        }

        parent::__construct($subject, $config);
    }

    /**
     * Send emails to the administrator and buyer of units.
     *
     * @param \stdClass $item
     * @param \stdClass $transaction
     * @param Registry $params
     */
    protected function sendMails($item, $transaction, $params)
    {
        // Get website
        $uri     = \JUri::getInstance();
        $website = $uri->toString(array('scheme', 'host'));

        $emailMode = $this->params->get('email_mode', 'plain');

        // Prepare data for parsing
        $data = array(
            'site_name'      => $this->app->get('sitename'),
            'site_url'       => \JUri::root(),
            'item_title'     => htmlentities($transaction->title, ENT_QUOTES, 'UTF-8'),
            'items_number'   => $transaction->units,
            'accounts_url'   => $website . \JRoute::_(\VirtualCurrencyHelperRoute::getAccountsRoute()),
            'txn_amount'     => $transaction->txn_amount,
            'txn_currency'   => htmlentities($transaction->txn_currency, ENT_QUOTES, 'UTF-8'),
        );

        // DEBUG DATA
        JDEBUG ? $this->log->add($this->textPrefix . '_DEBUG_MAIL_DATA', $this->debugType, var_export($data, true)) : null;

        // Send mail to the administrator
        $emailId = (int)$this->params->get('admin_mail_id', 0);
        if ($emailId > 0) {

            $email = new EmailTemplates\Email();
            $email->setDb(\JFactory::getDbo());
            $email->load($emailId);

            if (!$email->getSenderName()) {
                $email->setSenderName($this->app->get('fromname'));
            }
            if (!$email->getSenderEmail()) {
                $email->setSenderEmail($this->app->get('mailfrom'));
            }

            // Prepare recipient data.
            $componentParams = \JComponentHelper::getParams('com_virtualcurrency');
            /** @var  $componentParams Registry */

            $recipientId = (int)$componentParams->get('administrator_id', 0);
            if ($recipientId > 0) {
                $recipient     = \JFactory::getUser($recipientId);
                $recipientName = $recipient->get('name');
                $recipientMail = $recipient->get('email');
            } else {
                $recipientName = $this->app->get('fromname');
                $recipientMail = $this->app->get('mailfrom');
            }

            // Prepare data for parsing
            $data['sender_name']     = $email->getSenderName();
            $data['sender_email']    = $email->getSenderEmail();
            $data['recipient_name']  = $recipientName;
            $data['recipient_email'] = $recipientMail;

            $email->parse($data);
            $subject = $email->getSubject();
            $body    = $email->getBody($emailMode);

            $mailer = \JFactory::getMailer();
            if (strcmp('html', $emailMode) === 0) { // Send as HTML message
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, Prism\Constants::MAIL_MODE_HTML);
            } else { // Send as plain text.
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, Prism\Constants::MAIL_MODE_PLAIN);
            }

            // Check for an error.
            if ($return !== true) {
                $this->log->add(\JText::_($this->textPrefix . '_ERROR_MAIL_SENDING_ADMIN'), $this->debugType, $mailer->ErrorInfo);
            }

        }

        // Send mail to buyer.
        $emailId = (int)$this->params->get('user_mail_id', 0);
        if ($emailId > 0) {

            $email = new EmailTemplates\Email();
            $email->setDb(\JFactory::getDbo());
            $email->load($emailId);

            if (!$email->getSenderName()) {
                $email->setSenderName($this->app->get('fromname'));
            }
            if (!$email->getSenderEmail()) {
                $email->setSenderEmail($this->app->get('mailfrom'));
            }

            $user          = \JFactory::getUser($transaction->receiver_id);
            $recipientName = $user->get('name');
            $recipientMail = $user->get('email');

            // Prepare data for parsing
            $data['sender_name']     = $email->getSenderName();
            $data['sender_email']    = $email->getSenderEmail();
            $data['recipient_name']  = $recipientName;
            $data['recipient_email'] = $recipientMail;

            $email->parse($data);
            $subject = $email->getSubject();
            $body    = $email->getBody($emailMode);

            $mailer = \JFactory::getMailer();
            if (strcmp('html', $emailMode) === 0) { // Send as HTML message
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, Prism\Constants::MAIL_MODE_HTML);

            } else { // Send as plain text.
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, Prism\Constants::MAIL_MODE_PLAIN);

            }

            // Check for an error.
            if ($return !== true) {
                $this->log->add(\JText::_($this->textPrefix . '_ERROR_MAIL_SENDING_USER'), $this->debugType, $mailer->ErrorInfo);
            }
        }

    }

    protected function getCallbackUrl($htmlEncoded = false)
    {
        $page = \JString::trim($this->params->get('callback_url'));

        $uri    = \JUri::getInstance();
        $domain = $uri->toString(array('host'));

        // Encode to valid HTML.
        if ($htmlEncoded) {
            $page = str_replace('&', '&amp;', $page);
        }

        // Add the domain to the URL.
        if (false === strpos($page, $domain)) {
            $page = \JUri::root() . $page;
        }

        return $page;
    }

    protected function getReturnUrl()
    {
        $page = \JString::trim($this->params->get('return_url'));
        if (!$page) {
            $uri  = \JUri::getInstance();
            $page = $uri->toString(array('scheme', 'host')) . \JRoute::_(\VirtualCurrencyHelperRoute::getCartRoute('summary'), false);
        }

        return $page;
    }

    protected function getCancelUrl()
    {
        $page = \JString::trim($this->params->get('cancel_url'));
        if (!$page) {
            $uri  = \JUri::getInstance();
            $page = $uri->toString(array('scheme', 'host')) . \JRoute::_(\VirtualCurrencyHelperRoute::getCartRoute(), false);
        }

        return $page;
    }

    /**
     * Generate a system message.
     *
     * @param string $message
     * @param string $type
     * @param string $title
     *
     * @return string
     */
    protected function generateSystemMessage($message, $type = 'error', $title = '')
    {
        $html = '
        <div id="system-message-container">
			<div id="system-message">
                <div class="alert alert-'.$type.'">
                    <a data-dismiss="alert" class="close">×</a>
                    ';

        if ($title !== '') {
            $html .= '<h4 class="alert-heading">'.$title.'</h4>';
        }

        $html .= '  <div>
                        <p>' . htmlentities($message, ENT_QUOTES, 'UTF-8') . '</p>
                    </div>
                </div>
            </div>
	    </div>';

        return $html;
    }

    /**
     * This method returns payment session.
     *
     * @param array $options The keys used to load payment session data from database.
     *
     * @throws \UnexpectedValueException
     *
     * @return Virtualcurrency\Payment\Session
     */
    public function getPaymentSession(array $options)
    {
        $id        = ArrayHelper::getValue($options, 'id', 0, 'int');
        $sessionId = ArrayHelper::getValue($options, 'session_id');
        $uniqueKey = ArrayHelper::getValue($options, 'unique_key');

        // Prepare keys for anonymous user.
        if ($id > 0) {
            $keys = $id;
        } elseif ($sessionId !== '') {
            $keys = array(
                'session_id'   => $sessionId
            );
        } elseif ($uniqueKey !== '') { // Prepare keys to get record by unique key.
            $keys = array(
                'unique_key' => $uniqueKey,
            );
        } else {
            throw new \UnexpectedValueException('Invalid payment session key.');
        }

        $paymentSession = new Virtualcurrency\Payment\Session(\JFactory::getDbo());
        $paymentSession->load($keys);

        return $paymentSession;
    }

    /**
     * Check for valid payment gateway.
     *
     * @param string $gateway
     *
     * @return bool
     */
    protected function isValidPaymentGateway($gateway)
    {
        $value1 = \JString::strtolower($this->serviceAlias);
        $value2 = \JString::strtolower($gateway);

        return (bool)(\JString::strcmp($value1, $value2) === 0);
    }

    /**
     * This method is executed after complete payment.
     * It is used to be sent mails to user and administrator
     *
     * @param string $context  Transaction data
     * @param \stdClass $item  Item data
     * @param \stdClass $transaction  Transaction data
     * @param \stdClass $paymentSession Payment session data.
     * @param Registry $params Component parameters
     */
    public function onAfterPayment($context, &$item, &$transaction, &$paymentSession, &$params)
    {
        if (strcmp('com_crowdfunding.notify.' . $this->serviceAlias, $context) !== 0) {
            return;
        }

        if ($this->app->isAdmin()) {
            return;
        }

        $doc = \JFactory::getDocument();
        /**  @var $doc \JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp('raw', $docType) !== 0) {
            return;
        }

        // Send mails
        $this->sendMails($item, $transaction, $params);
    }

    /**
     * Prepare extra data.
     *
     * @param array  $data
     * @param string $note
     *
     * @return array
     */
    protected function prepareExtraData($data, $note = '')
    {
        $date        = new \JDate();
        $trackingKey = $date->toUnix();

        $extraData = array(
            $trackingKey => array()
        );

        foreach ($this->extraDataKeys as $key) {
            if (array_key_exists($key, $data)) {
                $extraData[$trackingKey][$key] = $data[$key];
            }
        }

        // Set a note.
        if (\JString::strlen($note) > 0) {
            $extraData[$trackingKey]['NOTE'] = $note;
        }

        return $extraData;
    }
}
