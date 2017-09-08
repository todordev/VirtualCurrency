<?php
/**
 * @package      Virtualcurrency\Payment
 * @subpackage   Plugin
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Virtualcurrency\Payment;

use Prism\Payment\Result as PaymentResult;
use Virtualcurrency\Payment\Session\Session as PaymentSession;
use Virtualcurrency\Payment\Session\Repository as PaymentSessionRepository;
use Prism\Database\Condition\Condition;
use Prism\Database\Condition\Conditions;
use Prism\Database\Request\Request;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Prism\Log;
use Prism\Constants as PrismConstants;
use Virtualcurrency;
use Emailtemplates;

/**
 * Virtualcurrency payment plugin class.
 *
 * @package      Virtualcurrency\Payment
 * @subpackage   Payments
 */
class Plugin extends \JPlugin
{
    protected $serviceProvider;
    protected $serviceAlias;

    protected $log;
    protected $textPrefix = 'PLG_VIRTUALCURRENCYPAYMENT';
    protected $debugType = 'DEBUG_PAYMENT_PLUGIN';

    protected $logFile = 'com_virtualcurrency.php';

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
        $this->app = \JFactory::getApplication();

        // Set file writer.
        if ($this->logFile and $this->logFile !== '') {
            // Create log object
            $this->log = new Log\Log();

            $file = \JPath::clean($this->app->get('log_path') . '/' . basename($this->logFile));
            $this->log->addAdapter(new Log\Adapter\File($file));
        }

        parent::__construct($subject, $config);
    }

    /**
     * Send emails to the administrator and buyer of units.
     *
     * @param PaymentResult $paymentResult
     * @param Registry      $params
     */
    protected function sendMails(PaymentResult $paymentResult, $params)
    {
        // Get website
        $uri     = \JUri::getInstance();
        $website = $uri->toString(array('scheme', 'host'));

        $emailMode = $this->params->get('email_mode', 'plain');

        $transaction = $paymentResult->transaction;

        // Prepare data for parsing
        $data = array(
            'site_name'    => $this->app->get('sitename'),
            'site_url'     => \JUri::root(),
            'item_title'   => htmlentities($transaction->title, ENT_QUOTES, 'UTF-8'),
            'items_number' => $transaction->units,
            'accounts_url' => $website . \JRoute::_(\VirtualcurrencyHelperRoute::getAccountsRoute()),
            'txn_amount'   => $transaction->txn_amount,
            'txn_currency' => htmlentities($transaction->txn_currency, ENT_QUOTES, 'UTF-8'),
        );

        // DEBUG DATA
        JDEBUG ? $this->log->add($this->textPrefix . '_DEBUG_MAIL_DATA', $this->debugType, var_export($data, true)) : null;

        // Send mail to the administrator
        $emailId = (int)$this->params->get('admin_mail_id', 0);
        if ($emailId > 0) {
            $email = new Emailtemplates\Email();
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
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, PrismConstants::MAIL_MODE_HTML);
            } else { // Send as plain text.
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, PrismConstants::MAIL_MODE_PLAIN);
            }

            // Check for an error.
            if ($return !== true) {
                $this->log->add(\JText::_($this->textPrefix . '_ERROR_MAIL_SENDING_ADMIN'), $this->debugType, $mailer->ErrorInfo);
            }
        }

        // Send mail to buyer.
        $emailId = (int)$this->params->get('user_mail_id', 0);
        if ($emailId > 0) {
            $email = new Emailtemplates\Email();
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
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, PrismConstants::MAIL_MODE_HTML);
            } else { // Send as plain text.
                $return = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, PrismConstants::MAIL_MODE_PLAIN);
            }

            // Check for an error.
            if ($return !== true) {
                $this->log->add(\JText::_($this->textPrefix . '_ERROR_MAIL_SENDING_USER'), $this->debugType, $mailer->ErrorInfo);
            }
        }
    }

    protected function getCallbackUrl($htmlEncoded = false)
    {
        $page = trim($this->params->get('callback_url'));

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
        $page = trim($this->params->get('return_url'));
        if (!$page) {
            $uri  = \JUri::getInstance();
            $page = $uri->toString(array('scheme', 'host')) . \JRoute::_(\VirtualcurrencyHelperRoute::getCartRoute('summary'), false);
        }

        return $page;
    }

    protected function getCancelUrl()
    {
        $page = trim($this->params->get('cancel_url'));
        if (!$page) {
            $uri  = \JUri::getInstance();
            $page = $uri->toString(array('scheme', 'host')) . \JRoute::_(\VirtualcurrencyHelperRoute::getCartRoute(), false);
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
                <div class="alert alert-' . $type . '">
                    <a data-dismiss="alert" class="close">×</a>
                    ';

        if ($title !== '') {
            $html .= '<h4 class="alert-heading">' . $title . '</h4>';
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
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return Virtualcurrency\Payment\Session\Session
     */
    public function getPaymentSession(array $options)
    {
        $id        = ArrayHelper::getValue($options, 'id', 0, 'int');
        $sessionId = ArrayHelper::getValue($options, 'session_id', '');
        $uniqueKey = ArrayHelper::getValue($options, 'unique_key', '');
        $orderId   = ArrayHelper::getValue($options, 'order_id', '');

        // Prepare conditions.
        if ($id > 0) {
            $conditionId = new Condition(['column' => 'id', 'value' => $id, 'operator' => '=', 'table' => 'a']);
            $conditions  = new Conditions();
            $conditions->addCondition($conditionId);

        } elseif ($sessionId !== '') {
            $conditionSession = new Condition(['column' => 'session_id', 'value' => $sessionId, 'operator' => '=', 'table' => 'a']);
            $conditions       = new Conditions();
            $conditions->addCondition($conditionSession);

        } elseif ($uniqueKey !== '' and $orderId !== '') { // Prepare keys to get record by unique key and order ID.
            $conditionUniqueKey = new Condition(['column' => 'unique_key', 'value' => $uniqueKey, 'operator' => '=', 'table' => 'a']);
            $conditionOrderId   = new Condition(['column' => 'order_id', 'value' => $orderId, 'operator' => '=', 'table' => 'a']);
            $conditions         = new Conditions();
            $conditions
                ->addCondition($conditionUniqueKey)
                ->addCondition($conditionOrderId);

        } elseif ($uniqueKey !== '') { // Prepare keys to get record by unique key.
            $conditionUniqueKey = new Condition(['column' => 'unique_key', 'value' => $uniqueKey, 'operator' => '=', 'table' => 'a']);
            $conditions         = new Conditions();
            $conditions->addCondition($conditionUniqueKey);

        } elseif ($orderId !== '') { // Prepare keys to get record by order ID.
            $conditionOrderId = new Condition(['column' => 'order_id', 'value' => $orderId, 'operator' => '=', 'table' => 'a']);
            $conditions       = new Conditions();
            $conditions->addCondition($conditionOrderId);

        } else {
            throw new \UnexpectedValueException('Invalid payment session key.');
        }

        // Prepare database request.
        $databaseRequest = new Request();
        $databaseRequest->setConditions($conditions);

        $mapper     = new Virtualcurrency\Payment\Session\Mapper(new Virtualcurrency\Payment\Session\Gateway\JoomlaGateway(\JFactory::getDbo()));
        $repository = new Virtualcurrency\Payment\Session\Repository($mapper);

        return $repository->fetch($databaseRequest);
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
        $value1 = strtolower($this->serviceAlias);
        $value2 = strtolower($gateway);

        return (strcmp($value1, $value2) === 0);
    }

    /**
     * This method is executed after complete payment notification.
     * It is used to be sent mails to users and the administrator.
     *
     * <code>
     * $paymentResult->transaction;
     * $paymentResult->paymentSession;
     * $paymentResult->serviceProvider;
     * $paymentResult->serviceAlias;
     * $paymentResult->response;
     * $paymentResult->returnUrl;
     * $paymentResult->message;
     * $paymentResult->triggerEvents;
     * </code>
     *
     * @param string        $context
     * @param PaymentResult $paymentResult Object that contains Transaction, Reward, Project, PaymentSession, etc.
     * @param Registry      $params        Component parameters
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \OutOfBoundsException
     */
    public function onAfterPaymentNotify($context, $paymentResult, $params)
    {
        if (!preg_match('/com_virtualcurrency\.(notify|payments).*\.' . $this->serviceAlias . '$/', $context)) {
            return;
        }

        if ($this->app->isAdmin()) {
            return;
        }

        // Check document type
        $docType = \JFactory::getDocument()->getType();
        if (!in_array($docType, array('raw', 'html'), true)) {
            return;
        }

        // Send mails
        $this->sendMails($paymentResult, $params);
    }

    /**
     * This method will be executed after all payment events, especially onAfterPaymentNotify.
     * It is used to close payment session.
     *
     * <code>
     * $paymentResult->transaction;
     * $paymentResult->paymentSession;
     * $paymentResult->serviceProvider;
     * $paymentResult->serviceAlias;
     * $paymentResult->response;
     * $paymentResult->returnUrl;
     * $paymentResult->message;
     * $paymentResult->triggerEvents;
     * </code>
     *
     * @param string    $context
     * @param \stdClass $paymentResult Object that contains Transaction, Reward, Project, PaymentSession, etc.
     * @param Registry  $params        Component parameters
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function onAfterPayment($context, $paymentResult, $params)
    {
        if (!preg_match('/com_virtualcurrency\.(notify|payments).*\.' . $this->serviceAlias . '$/', $context)) {
            return;
        }

        if ($this->app->isAdmin()) {
            return;
        }

        // Check document type
        $docType = \JFactory::getDocument()->getType();
        if (!in_array($docType, array('raw', 'html'), true)) {
            return;
        }

        $paymentSession = $paymentResult->paymentSession;
        /** @var PaymentSession $paymentSession */

        // Remove payment session record from database.
        if (($paymentSession instanceof PaymentSession) and $paymentSession->getId()) {
            $gateway    = new VirtualCurrency\Payment\Session\Gateway\JoomlaGateway(\JFactory::getDbo());
            $repository = new PaymentSessionRepository(new VirtualCurrency\Payment\Session\Mapper($gateway));

            $repository->delete($paymentSession);
        }
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
        if (strlen($note) > 0) {
            $extraData[$trackingKey]['NOTE'] = $note;
        }

        return $extraData;
    }
}
